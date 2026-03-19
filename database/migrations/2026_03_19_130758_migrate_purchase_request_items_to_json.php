<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing items_purchase_request data to JSON in purchase_request table
        $chunkSize = 100;

        DB::table('purchase_request')->orderBy('id')->chunkById($chunkSize, function ($purchaseRequests) {
            $prIds = $purchaseRequests->pluck('id')->toArray();

            $pivotItemsByPr = DB::table('items_purchase_request')
                ->whereIn('purchase_request_id', $prIds)
                ->get()
                ->groupBy('purchase_request_id');

            $itemsMap = [];
            $unitsMap = [];

            $itemIds = $pivotItemsByPr->flatten()->pluck('item_id')->unique()->filter()->toArray();

            if (!empty($itemIds)) {
                $itemsMap = DB::table('items')
                    ->whereIn('id', $itemIds)
                    ->get()
                    ->keyBy('id')
                    ->map(function ($item) {
                        return [
                            'name' => $item->name,
                            'unit_id' => $item->unit_id,
                        ];
                    })
                    ->toArray();

                $unitIds = collect($itemsMap)->pluck('unit_id')->unique()->filter()->toArray();

                if (!empty($unitIds)) {
                    $unitsMap = DB::table('units')
                        ->whereIn('id', $unitIds)
                        ->pluck('name', 'id')
                        ->toArray();
                }
            }

            foreach ($purchaseRequests as $pr) {
                $itemsJson = [];

                if (isset($pivotItemsByPr[$pr->id])) {
                    $itemsJson = $pivotItemsByPr[$pr->id]->map(function ($pivot) use ($itemsMap, $unitsMap) {
                        $itemInfo = $itemsMap[$pivot->item_id] ?? null;
                        $unitName = null;

                        if ($itemInfo && !empty($itemInfo['unit_id']) && isset($unitsMap[$itemInfo['unit_id']])) {
                            $unitName = $unitsMap[$itemInfo['unit_id']];
                        }

                        return [
                            'item_name'   => $itemInfo ? $itemInfo['name'] : 'Unknown Item',
                            'quantity'    => $pivot->quantity,
                            'unit'        => $unitName,
                            'description' => $pivot->description,
                        ];
                    })->toArray();
                }

                DB::table('purchase_request')
                    ->where('id', $pr->id)
                    ->update([
                        'items' => json_encode($itemsJson),
                    ]);
            }
        });

        // Drop pivot table AFTER data migration, outside transaction
        Schema::dropIfExists('items_purchase_request');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate pivot table outside transaction
        Schema::create('items_purchase_request', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('quantity');
            $table->foreignUuid('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_request')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Move JSON data back into pivot table
        $purchaseRequests = DB::table('purchase_request')->whereNotNull('items')->get();

        foreach ($purchaseRequests as $pr) {
            $items = json_decode($pr->items, true);

            if (!is_array($items)) {
                \Log::warning("Failed to decode items JSON for purchase_request {$pr->id}: " . json_last_error_msg());
                continue;
            }

            foreach ($items as $itemData) {
                $itemId = null;

                if (!empty($itemData['item_id'])) {
                    $itemId = $itemData['item_id'];
                } else {
                    $item = DB::table('items')
                        ->where('name', $itemData['item_name'] ?? null)
                        ->first();

                    $itemId = $item ? $item->id : null;
                }

                DB::table('items_purchase_request')->insert([
                    'description'         => $itemData['description'] ?? null,
                    'quantity'            => $itemData['quantity'] ?? 0,
                    'item_id'             => $itemId,
                    'purchase_request_id' => $pr->id,
                    'created_by'          => $pr->created_by ?? null,
                    'updated_by'          => $pr->updated_by ?? null,
                    'created_at'          => $pr->created_at,
                    'updated_at'          => $pr->updated_at,
                ]);
            }
        }
    }
};