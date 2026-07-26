<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\MachinesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\PMScheduleController;
use App\Http\Controllers\SparepartController;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $query = Machine::query();

        // SEARCH
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('machine_number', 'like', '%' . $request->search . '%')
                  ->orWhere('machine_type', 'like', '%' . $request->search . '%')
                  ->orWhere('area', 'like', '%' . $request->search . '%');
            });
        }

        // FILTER TYPE
        if ($request->filled('machine_type')) {
            $query->where('machine_type', $request->machine_type);
        }

        // FILTER AREA
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        // FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔥 SORTING (NEW)
        if ($request->filled('sort')) {

            if ($request->sort == 'machine_number_asc') {
                $query->orderBy('machine_number', 'asc');
            }

            if ($request->sort == 'machine_number_desc') {
                $query->orderBy('machine_number', 'desc');
            }

            if ($request->sort == 'area_asc') {
                $query->orderBy('area', 'asc');
            }

            if ($request->sort == 'area_desc') {
                $query->orderBy('area', 'desc');
            }

        } else {
            // default sort CMMS style
            $query->orderBy('machine_number', 'asc');
        }

        $machines = $query->paginate(20)->withQueryString();

        // dropdown filter
        $machineTypes = Machine::select('machine_type')->distinct()->get();
        $areas = Machine::select('area')->distinct()->get();

        return view('machines.index', compact(
            'machines',
            'machineTypes',
            'areas'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();

        if ($ext !== 'csv') {
            return back()->with('error', 'Only CSV file is allowed!');
        }

        Excel::import(new MachinesImport(), $file);

        return back()->with('success', 'Machine imported successfully');
    }

    public function importForm()
    {
        return view('machines.import');
    }

    public function create()
    {
        return view('machines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required',
            'machine_type' => 'required',
            'machine_number' => 'required|unique:machines',
            'description' => 'nullable',
            'status' => 'required',
        ]);

        Machine::create($request->all());

        return redirect()
            ->route('machines.index')
            ->with('success', 'Machine created successfully');
    }

    public function edit(Machine $machine)
    {
        return view(
            'machines.edit',
            compact('machine')
        );
    }

    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            'area' => 'required',
            'machine_type' => 'required',
            'machine_number' => 'required|unique:machines,machine_number,' . $machine->id,
            'description' => 'nullable',
            'status' => 'required',
            'pm_cycle_value' => 'nullable|integer|min:1',
            'pm_cycle_unit' => 'nullable|in:DAY,WEEK,MONTH,HOUR',
        ]);

        $machine->update($request->all());

        return redirect()
        ->route('machines.index')
        ->with('success', 'Machine updated successfully.');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();

        return back()->with('success', 'Machine deleted');
    }
}
