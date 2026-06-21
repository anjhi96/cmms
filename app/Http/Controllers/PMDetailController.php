<?php

namespace App\Http\Controllers;

use App\Models\PMDetail;
use App\Models\PMSchedule;
use Illuminate\Http\Request;

class PMDetailController extends Controller
{
    /**
     * Simpan semua measurement PM Detail
     */
    public function store(Request $request, $pmScheduleId)
    {
        $pmSchedule = PMSchedule::findOrFail($pmScheduleId);

        $request->validate([
            'details' => 'required|array',
            'details.*.item' => 'required|string',
            'details.*.value' => 'nullable|string',
            'details.*.unit' => 'nullable|string',
        ]);

        foreach ($request->details as $detail) {

            PMDetail::create([
                'pm_schedule_id'   => $pmSchedule->id,
                'measurement_item' => $detail['item'],
                'measurement_value'=> $detail['value'] ?? null,
                'unit'             => $detail['unit'] ?? null,
            ]);
        }

        return redirect()
            ->route('pm-schedules.edit', $pmScheduleId)
            ->with('success', 'PM Detail saved successfully');
    }

    /**
     * Optional: delete single detail (kalau user salah input)
     */
    public function destroy($id)
    {
        $detail = PMDetail::findOrFail($id);
        $detail->delete();

        return back()->with('success', 'PM Detail deleted');
    }
}