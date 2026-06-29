<?php

namespace App\Http\Controllers;

use App\Models\MachineProblem;
use Illuminate\Http\Request;
use App\Models\Machine;

class MachineProblemController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineProblem::query();

        // FILTER
        if ($request->machine_type) {
            $query->where('machine_type', $request->machine_type);
        }

        if ($request->problem) {
            $query->where('problem', $request->problem);
        }

        // SORT
        $sort = $request->get('sort');

        switch ($sort) {
            case 'machine_type_asc':
                $query->orderBy('machine_type', 'asc');
                break;

            case 'machine_type_desc':
                $query->orderBy('machine_type', 'desc');
                break;

            case 'problem_asc':
                $query->orderBy('problem', 'asc');
                break;

            case 'problem_desc':
                $query->orderBy('problem', 'desc');
                break;

            default:
                $query->orderBy('machine_type', 'asc');
        }

        $machineProblems = $query->get();

        // 🔥 INI YANG KAMU LUPA
        $machines = Machine::orderBy('machine_type')->get();

        return view('machine-problems.index', compact(
            'machineProblems',
            'machines'
        ));
    }


    public function create()
    {
        $machines = Machine::select('machine_type')
        ->distinct()
        ->orderBy('machine_type')
        ->get();

        return view('machine-problems.create', compact('machines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_type' => 'required',
            'problems' => 'required|array',
            'problems.*' => 'required|string'
        ], [
            'machine_type.required' => 'Machine type must be selected',
            'problems.required' => 'At least one problem is required',
        ]);

        foreach ($request->problems as $problem) {

            if (!$problem) {
                continue;
            }

            // 🔥 ANTI DUPLICATE CHECK
            $exists = MachineProblem::where('machine_type', $request->machine_type)
            ->where('problem', trim($request->problem))
            ->where('id', '!=', $machineProblem->id)
            ->exists();

            if ($exists) {
                continue; // skip duplicate
            }

            MachineProblem::create([
                'machine_type' => $request->machine_type,
                'problem' => $problem
            ]);
        }

        return redirect()
            ->route('machine-problems.index')
            ->with('success', 'Problems saved successfully');
    }

    public function edit(MachineProblem $machineProblem)
    {
        $machines = Machine::select('machine_type')
            ->distinct()
            ->orderBy('machine_type')
            ->get();

        return view(
            'machine-problems.edit',
            compact('machineProblem', 'machines')
        );
    }

    public function update(Request $request, MachineProblem $machineProblem)
    {
        $request->validate([
            'machine_type' => 'required',
            'problem'      => 'required'
        ]);

        // cek duplicate selain data yang sedang diedit
        $exists = MachineProblem::where(
            'machine_type',
            $request->machine_type
        )
            ->where(
                'problem',
                trim($request->problem)
            )
            ->where('id', '!=', $machineProblem->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Problem already exists for this machine type.');
        }

        $machineProblem->update([
            'machine_type' => $request->machine_type,
            'problem'      => trim($request->problem),
        ]);

        return redirect()
            ->route('machine-problems.index')
            ->with('success', 'Problem updated successfully');
    }

    public function destroy(MachineProblem $machineProblem)
    {
        $machineProblem->delete();

        return back()->with('success', 'Problem deleted');
    }
}
