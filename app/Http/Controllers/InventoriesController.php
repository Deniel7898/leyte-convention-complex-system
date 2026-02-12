<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\QR_Code;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;

class InventoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Get all consumables and non-consumables with relationships
        $consumables = InventoryConsumable::with(['item', 'qr_code'])->get();
        $nonConsumables = InventoryNonConsumable::with(['item', 'qr_code'])->get();

        // Standardize the attributes so both can be displayed in one table
        $consumables = $consumables->map(function ($c) {
            return (object)[
                'item' => $c->item,
                'receive_date' => $c->receive_date,
                'qr_code' => $c->qr_code,
                'description' => $c->description ?? '--',
                'warranty_expires' => $c->warranty_expires ?? '--',
                'type' => 'Consumable',
            ];
        });

        $nonConsumables = $nonConsumables->map(function ($n) {
            return (object)[
                'item' => $n->item,
                'receive_date' => $n->receive_date,
                'qr_code' => $n->qr_code,
                'description' => $n->description ?? '--',
                'warranty_expires' => $n->warranty_expires ?? '--',
                'type' => 'Non-Consumable',
            ];
        });

        // Merge both collections
        $inventories = $consumables->merge($nonConsumables)
            ->sortByDesc('receive_date') // Optional sorting
            ->values(); // Reset keys

        // Pass to your table partial
        $inventories_table = view('inventory.inventory.table', compact('inventories'))->render();

        // Return main view with the table
        return view('inventory.inventory.index', compact('inventories_table'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.inventory.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
