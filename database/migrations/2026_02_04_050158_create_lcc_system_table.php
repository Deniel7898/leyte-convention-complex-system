<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Units
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Items
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('type')->default(0); // 0 = consumable, 1 = non-consumable
            $table->text('description')->nullable();
            $table->boolean('status')->default(1);
            $table->string('quantity')->default('1');
            $table->string('picture')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Inventory Consumable
        Schema::create('inventory_consumable', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('received_date')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Inventory Non-Consumable
        Schema::create('inventory_non_consumable', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('received_date')->nullable();
            $table->date('warranty_expires')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Item Distributions
        Schema::create('item_distributions', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(0);
            $table->text('description')->nullable();
            $table->string('quantity');
            $table->date('distribution_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->enum('status', ['pending', 'distributed', 'partial', 'borrowed', 'returned', 'received'])->default('distributed');
            $table->text('remarks')->nullable();
            $table->foreignUuid('inventory_consumable_id')->nullable()->constrained('inventory_consumable')->onDelete('set null');
            $table->foreignUuid('inventory_non_consumable_id')->nullable()->constrained('inventory_non_consumable')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Service Records
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('quantity');
            $table->date('schedule_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->text('encharge_person')->nullable();
            $table->string('picture')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignUuid('inventory_non_consumable_id')->nullable()->constrained('inventory_non_consumable')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Purchase Request
        Schema::create('purchase_request', function (Blueprint $table) {
            $table->id();
            $table->date('request_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'ordered', 'received'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Items-Purchase Request pivot
        Schema::create('items_purchase_request', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('quantity');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_request')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // QR Codes
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('inventory_consumable_id')->nullable()->constrained('inventory_consumable')->onDelete('cascade');
            $table->foreignUuid('inventory_non_consumable_id')->nullable()->constrained('inventory_non_consumable')->onDelete('cascade');
            $table->string('code')->unique();
            $table->enum('status', ['active', 'used', 'expired'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Drop tables in reverse order to avoid FK issues.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('item_distributions');
        Schema::dropIfExists('service_records');
        Schema::dropIfExists('inventory_non_consumable');
        Schema::dropIfExists('inventory_consumable');
        Schema::dropIfExists('items_purchase_request');
        Schema::dropIfExists('purchase_request');
        Schema::dropIfExists('items');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
    }
};
