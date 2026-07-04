<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineProblemFinding;
use App\Models\MachineProblem;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MachineProblemFindingImport;
use App\Http\Controllers\MachineProblemFindingController;

class MachineProblemFindingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        $query = MachineProblemFinding::query();

        // SEARCH
        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where('finding', 'like', "%{$request->search}%")
                  ->orWhere('category', 'like', "%{$request->search}%");

            });

        }

        // FILTER CATEGORY
        if ($request->filled('category')) {

            $query->where('category', $request->category);

        }

        // SORT
        switch ($request->sort) {

            case 'category_asc':
                $query->orderBy('category');
                break;

            case 'category_desc':
                $query->orderByDesc('category');
                break;

            case 'finding_asc':
                $query->orderBy('finding');
                break;

            case 'finding_desc':
                $query->orderByDesc('finding');
                break;

            default:
                $query->orderBy('category')
                      ->orderBy('finding');

        }

        $findings = $query->paginate(20)->withQueryString();

        $categories = MachineProblem::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'machine-problem-findings.index',
            compact(
                'findings',
                'categories'
            )
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(
            new MachineProblemFindingImport(),
            $request->file('file')
        );

        return back()->with(
            'success',
            'Machine Problem Findings imported successfully.'
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = MachineProblem::select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

        return view(
            'machine-problem-findings.create',
            compact('categories')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'category' => 'required',

            'finding' => 'required',

        ]);

        MachineProblemFinding::create($request->all());

        return redirect()
            ->route('machine-problem-findings.index')
            ->with('success', 'Finding created successfully.');
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
    public function edit(MachineProblemFinding $machineProblemFinding)
    {
        $categories = MachineProblem::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'machine-problem-findings.edit',
            compact(
                'machineProblemFinding',
                'categories'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        MachineProblemFinding $machineProblemFinding
    ) {
        $request->validate([

            'category' => 'required',

            'finding' => 'required',

        ]);

        $exists = MachineProblemFinding::where('category', $request->category)
            ->where('finding', $request->finding)
            ->where('id', '!=', $machineProblemFinding->id)
            ->exists();

        if ($exists) {

            return back()
                ->withErrors([
                    'finding' => 'Finding already exists in this category.'
                ])
                ->withInput();

        }

        $machineProblemFinding->update([
            'category' => $request->category,
            'finding' => $request->finding,

        ]);

        return redirect()
            ->route('machine-problem-findings.index')
            ->with('success', 'Finding updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineProblemFinding $machineProblemFinding)
    {
        $machineProblemFinding->delete();

        return back()->with(
            'success',
            'Finding deleted successfully.'
        );
    }
}
