<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineMeasurement;
use App\Models\Machine;

class MachineMeasurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MachineMeasurement::query();

        // FILTER MACHINE TYPE
        if ($request->filled('machine_type')) {
            $query->where('machine_type', $request->machine_type);
        }

        // filter measurement
        if ($request->filled('measurement')) {
            $query->where('measurement_item', $request->measurement);
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

            case 'measurement_asc':
                $query->orderBy('measurement_item', 'asc');
                break;

            case 'measurement_desc':
                $query->orderBy('measurement_item', 'desc');
                break;

            default:
                $query->orderBy('machine_type', 'asc');
        }

        $measurements = $query->paginate(20);

        $machines = Machine::select('machine_type')
            ->distinct()
            ->orderBy('machine_type')
            ->get();

        return view(
            'machine-measurements.index',
            compact('measurements', 'machines')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $machines = Machine::select('machine_type')
        ->distinct()
        ->orderBy('machine_type')
        ->get();

        return view(
            'machine-measurements.create',
            compact('machines')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'machine_type' => 'required',
            'measurements' => 'required|array|min:1',
        ]);

        foreach ($request->measurements as $index => $item) {

            if (!$item) {
                continue;
            }

            $exists = MachineMeasurement::where(
                'machine_type',
                $request->machine_type
            )
            ->where(
                'measurement_item',
                trim($item)
            )
            ->exists();

            if (!$exists) {

                MachineMeasurement::create([
                    'machine_type'     => $request->machine_type,
                    'measurement_item' => trim($item),
                    'unit'             => $request->units[$index] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('machine-measurements.index')
            ->with('success', 'Measurement added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MachineMeasurement $machineMeasurement)
    {
        $machines = Machine::select('machine_type')
            ->distinct()
            ->orderBy('machine_type')
            ->get();

        return view(
            'machine-measurements.edit',
            compact('machineMeasurement', 'machines')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineMeasurement $machineMeasurement)
    {
        $request->validate([
            'machine_type'     => 'required',
            'measurement_item' => 'required',
            'unit'             => 'nullable',
        ]);

        // cek duplicate selain data yang sedang diedit
        $exists = MachineMeasurement::where(
            'machine_type',
            $request->machine_type
        )
            ->where(
                'measurement_item',
                trim($request->measurement_item)
            )
            ->where('id', '!=', $machineMeasurement->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Measurement item already exists for this machine type.');
        }

        $machineMeasurement->update([
            'machine_type'     => $request->machine_type,
            'measurement_item' => trim($request->measurement_item),
            'unit'             => trim($request->unit),
        ]);

        return redirect()
            ->route('machine-measurements.index')
            ->with('success', 'Measurement updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $measurement = MachineMeasurement::findOrFail($id);

        $measurement->delete();

        return redirect()
            ->route('machine-measurements.index')
            ->with('success', 'Measurement deleted successfully');
    }
}
