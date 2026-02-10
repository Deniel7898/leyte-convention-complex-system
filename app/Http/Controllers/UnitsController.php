<?php

namespace App\Http\Controllers;
use App\Models\Units;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Units::all();
        $units_table = view('reference.units.table', compact('units'))->render();
        return view('reference.units.index', compact('units_table')); 
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
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validate['created_by'] = auth()->user()->id;
        $validate['updated_by'] = auth()->user()->id;

        Units::create($validate);

        $units = Units::all();
        $units_table = view('reference.units.table', compact('units'))->render();
        return $units_table;
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
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validate['updated_by'] = auth()->user()->id;

        Units::where('id', $id)->update($validate);

        $units = Units::all();
        $units_table = view('reference.units.table', compact('units'))->render();
        return $units_table;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Units::destroy($id);

        $units = Units::all();
        $units_table = view('reference.units.table', compact('units'))->render();
        return $units_table;
    }
}