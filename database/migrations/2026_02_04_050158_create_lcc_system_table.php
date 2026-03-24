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
            $table->enum('type', ['consumable', 'non-consumable'])->nullable();
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
            $table->text('abbreviation')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Items
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['consumable', 'non-consumable'])->nullable();
            $table->text('description')->nullable();
            $table->integer('total_stock');
            $table->integer('remaining');
            $table->string('picture')->nullable();
            $table->string('supplier')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Inventory 
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', ['available', 'borrowed', 'issued', 'under repair', 'distributed', 'maintenance', 'installation', 'inspection'])->nullable();
            $table->string('holder')->nullable();
            $table->date('received_date')->nullable();
            $table->date('date_assigned')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        //Inventory History
        Schema::create('inventory_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('holder_or_borrower')->nullable();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignUuid('inventory_id')->nullable()->constrained('inventories')->onDelete('cascade');
            $table->enum('action', ['item created', 'added stock', 'added unit', 'distributed', 'borrowed', 'issued', 'returned', 'maintenance', 'installation', 'inspection', 'service completed', 'deleted'])->nullable();
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Item Distributions
        Schema::create('item_distributions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_id');
            $table->enum('type', ['distributed', 'borrowed', 'issued'])->nullable();
            $table->integer('quantity');
            $table->string('department_or_borrower');
            $table->date('distribution_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->enum('status', ['completed', 'borrowed', 'returned', 'issued'])->nullable();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignUuid('inventory_id')->nullable()->constrained('inventories')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Service Records
        Schema::create('service_records', function (Blueprint $table) {
            $table->uuid('id')->primary(); // use UUID
            $table->enum('type', ['maintenance', 'installation', 'inspection'])->nullable();
            $table->text('description')->nullable();
            $table->date('service_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->string('technician')->nullable();
            $table->enum('status', ['scheduled', 'under repair', 'completed', 'cancelled'])->nullable();
            $table->string('picture')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignUuid('inventory_id')->nullable()->constrained('inventories')->onDelete('cascade');
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
            $table->integer('quantity');
            $table->foreignUuid('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_request')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // QR Codes
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignUuid('inventory_id')->nullable()->constrained('inventories')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('qr_picture')->nullable();
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
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('items_purchase_request');
        Schema::dropIfExists('purchase_request');
        Schema::dropIfExists('items');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
    }
};
