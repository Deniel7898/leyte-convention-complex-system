<?php

namespace App\Http\Controllers;

use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitsController extends Controller
{
    /**
     * Helper: Get paginated categories table
     */
    private function getUnitsTable($perPage = 10)
    {
        $units = Units::paginate($perPage);
        return view('reference.units.table', compact('units'))->render();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Units::paginate(10);

        return view('reference.units.index', [
            'units' => $units,
            'units_table' => view('reference.units.table', compact('units'))->render()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reference.units.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        units::create($validated);

        return $this->getUnitsTable();
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
    public function edit(string $id)
    {
        $unit = Units::findOrFail($id);
        return view('reference.units.form', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        Units::updateOrCreate(['id' => $id], $validated);

        return $this->getUnitsTable();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unit = Units::findOrFail($id);
        $unit->delete();

        return $this->getUnitsTable();
    }
}
