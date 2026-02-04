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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('availability');
            $table->string('quantity');
            $table->string('remaining');
            $table->string('picture')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_consumable', function (Blueprint $table) {
            $table->id();
            $table->date('receive_date')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('qr_code_id')->nullable()->constrained('qr_codes')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_non_consumable', function (Blueprint $table) {
            $table->id();
            $table->date('warranty_expires')->nullable();
            $table->date('receive_date')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('qr_code_id')->nullable()->constrained('qr_codes')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('quantity');
            $table->date('schedule_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->text('encharge_person')->nullable();
            $table->string('picture')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('inventory_non_consumable_id')->nullable()->constrained('inventory_non_consumable')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('item_distributions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['distribution', 'borrow'])->default('distribution');
            $table->text('description')->nullable();
            $table->string('quantity');
            $table->date('distribution_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->enum('status', ['pending', 'distributed', 'borrowed', 'returned', 'received'])->default('distributed');
            $table->text('remarks')->nullable();
            $table->foreignId('inventory_consumable_id')->nullable()->constrained('inventory_consumable')->onDelete('set null');
            $table->foreignId('inventory_non_consumable_id')->nullable()->constrained('inventory_non_consumable')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_request', function (Blueprint $table) {
            $table->id();
            $table->date('request_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'ordered', 'received'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

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

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('status', ['active', 'used', 'expired'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('inventory_consumable');
        Schema::dropIfExists('inventory_non_consumable');
        Schema::dropIfExists('service_records');
        Schema::dropIfExists('item_distribution');
        Schema::dropIfExists('purchase_request');
        Schema::dropIfExists('item_purchase_request');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('units');
    }
};
