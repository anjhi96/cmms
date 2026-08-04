<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\OilAudit;
use App\Models\OilAuditFollowUp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OilAuditController extends Controller
{
    public function scan(): View
    {
        return view('oil-audits.scan');
    }

    public function entry(string $machineNumber): View
    {
        $machine = Machine::where('machine_number', trim($machineNumber))->firstOrFail();

        return view('oil-audits.entry', compact('machine'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'machine_id' => ['required', 'exists:machines,id'],
            'condition' => ['required', 'in:'.implode(',', array_keys(OilAudit::CONDITION_LABELS))],
        ]);

        $machine = Machine::findOrFail($validated['machine_id']);
        $user = $request->user();

        OilAudit::create([
            'machine_id' => $machine->id,
            'machine_number' => $machine->machine_number,
            'machine_type' => $machine->machine_type,
            'area' => $machine->area,
            'condition' => $validated['condition'],
            'audited_by_user_id' => $user->id,
            'audited_by_name' => $user->name,
            'audited_at' => now(),
        ]);

        return redirect()
            ->route('oil-audits.scan')
            ->with('success', "Audit oli {$machine->machine_number} berhasil disimpan. Siap scan mesin berikutnya.");
    }

    public function report(Request $request): View
    {
        $machines = Machine::query()
            ->with(['latestOilAudit.followUp'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($machineQuery) use ($search) {
                    $machineQuery->where('machine_number', 'like', "%{$search}%")
                        ->orWhere('machine_type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('area'), fn ($query) => $query->where('area', $request->input('area')))
            ->when($request->filled('machine_type'), fn ($query) => $query->where('machine_type', $request->input('machine_type')))
            ->when($request->filled('condition'), function ($query) use ($request) {
                $query->whereHas('latestOilAudit', fn ($auditQuery) => $auditQuery->where('condition', $request->input('condition')));
            })
            ->when($request->boolean('follow_up'), function ($query) {
                $query->whereHas('latestOilAudit', function ($auditQuery) {
                    $auditQuery->requiringFollowUp()->whereDoesntHave('followUp');
                });
            })
            ->orderBy('machine_number')
            ->paginate(20)
            ->withQueryString();

        $areas = Machine::query()->select('area')->distinct()->orderBy('area')->pluck('area');
        $machineTypes = Machine::query()->select('machine_type')->distinct()->orderBy('machine_type')->pluck('machine_type');

        $summary = [
            'today' => OilAudit::whereDate('audited_at', today())->count(),
            'pending' => OilAudit::requiringFollowUp()->whereDoesntHave('followUp')->count(),
            'critical' => OilAudit::where('condition', 'KRITIS')->whereDoesntHave('followUp')->count(),
        ];

        $pendingAudits = OilAudit::query()
            ->requiringFollowUp()
            ->whereDoesntHave('followUp')
            ->with('machine')
            ->latest('audited_at')
            ->limit(8)
            ->get();

        return view('oil-audits.report', compact(
            'machines',
            'areas',
            'machineTypes',
            'summary',
            'pendingAudits'
        ));
    }

    public function history(string $machineNumber): View
    {
        $machine = Machine::where('machine_number', trim($machineNumber))->firstOrFail();
        $audits = $machine->oilAudits()
            ->with('followUp')
            ->latest('audited_at')
            ->paginate(15);

        $latestAudit = $machine->latestOilAudit()->with('followUp')->first();
        $recentAudits = $machine->oilAudits()
            ->latest('audited_at')
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        return view('oil-audits.history', [
            'machine' => $machine,
            'audits' => $audits,
            'latestAudit' => $latestAudit,
            'recentAudits' => $recentAudits,
            'problemOptions' => OilAudit::PROBLEM_OPTIONS,
        ]);
    }

    public function storeFollowUp(Request $request, OilAudit $oilAudit): RedirectResponse
    {
        abort_unless($oilAudit->needsFollowUp(), 422, 'Follow up hanya diperlukan untuk kondisi oli yang tidak oke.');

        if ($oilAudit->followUp()->exists()) {
            return back()->with('warning', 'Follow up untuk audit ini sudah disimpan.');
        }

        $validated = $request->validate([
            'problem' => ['required', 'in:'.implode(',', OilAudit::PROBLEM_OPTIONS)],
            'action_taken' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        OilAuditFollowUp::create([
            'oil_audit_id' => $oilAudit->id,
            'problem' => $validated['problem'],
            'action_taken' => $validated['action_taken'],
            'pic_user_id' => $user->id,
            'pic_name' => $user->name,
            'actioned_at' => now(),
        ]);

        return back()->with('success', 'Tindak lanjut berhasil disimpan dan tercatat pada riwayat mesin.');
    }
}
