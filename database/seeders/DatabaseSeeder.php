<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        // -------------------------------------------------
        // USERS
        // -------------------------------------------------
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'last_name' => 'Administrator',
                'first_name' => 'System',
                'middle_name' => 'A',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'user1@example.com'],
            [
                'last_name' => 'Doe',
                'first_name' => 'John',
                'middle_name' => 'D',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'status' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'user2@example.com'],
            [
                'last_name' => 'Smith',
                'first_name' => 'Jane',
                'middle_name' => 'S',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'status' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $adminId = DB::table('users')->where('email', 'admin@example.com')->value('id');

        // -------------------------------------------------
        // CLEAR DEMO TABLES
        // -------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('qr_codes')->truncate();
        DB::table('item_distributions')->truncate();
        DB::table('service_records')->truncate();
        DB::table('inventory_history')->truncate();
        DB::table('inventories')->truncate();
        DB::table('purchase_request')->truncate();
        DB::table('items')->truncate();
        DB::table('units')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // -------------------------------------------------
        // CATEGORIES
        // -------------------------------------------------
        $categoryIds = [];

        $categories = [
            [
                'name' => 'Disposable',
                'type' => 'consumable',
                'description' => 'Single-use or consumable items',
            ],
            [
                'name' => 'Plumbing Mats',
                'type' => 'non-consumable',
                'description' => 'Protective mats used in plumbing work',
            ],
            [
                'name' => 'Tools',
                'type' => 'non-consumable',
                'description' => 'Tools and equipment',
            ],
            [
                'name' => 'Electric Mats',
                'type' => 'non-consumable',
                'description' => 'Electrical mats and accessories',
            ],
            [
                'name' => 'Generator Fanbelt',
                'type' => 'consumable',
                'description' => 'Fanbelt used for generator maintenance and replacement',
            ],
        ];

        foreach ($categories as $category) {
            $id = DB::table('categories')->insertGetId([
                'name' => $category['name'],
                'type' => $category['type'],
                'description' => $category['description'],
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $categoryIds[$category['name']] = $id;
        }

        // -------------------------------------------------
        // UNITS
        // -------------------------------------------------
        $unitIds = [];

        $units = [
            ['name' => 'Piece', 'description' => 'Piece unit', 'abbreviation' => 'pc'],
            ['name' => 'Pack', 'description' => 'Pack unit', 'abbreviation' => 'pk'],
            ['name' => 'Box', 'description' => 'Box unit', 'abbreviation' => 'bx'],
            ['name' => 'Set', 'description' => 'Set unit', 'abbreviation' => 'st'],
            ['name' => 'Liter', 'description' => 'Liter unit', 'abbreviation' => 'L'],
            ['name' => 'Kilogram', 'description' => 'Kilogram unit', 'abbreviation' => 'kg'],
        ];

        foreach ($units as $unit) {
            $id = DB::table('units')->insertGetId([
                'name' => $unit['name'],
                'description' => $unit['description'],
                'abbreviation' => $unit['abbreviation'],
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $unitIds[$unit['name']] = $id;
        }

        // -------------------------------------------------
        // ITEMS
        // -------------------------------------------------
        $itemIds = [];

        $items = [
            [
                'name' => 'Electrical Tape',
                'type' => 'consumable',
                'description' => 'Black electrical tape for maintenance work',
                'total_stock' => 120,
                'remaining' => 85,
                'supplier' => 'Leyte Industrial Supply',
                'category' => 'Disposable',
                'unit' => 'Pack',
            ],
            [
                'name' => 'Generator Fanbelt B-54',
                'type' => 'consumable',
                'description' => 'Replacement fanbelt for generator set',
                'total_stock' => 12,
                'remaining' => 2,
                'supplier' => 'PowerCore Trading',
                'category' => 'Generator Fanbelt',
                'unit' => 'Piece',
            ],
            [
                'name' => 'Pipe Wrench 14in',
                'type' => 'non-consumable',
                'description' => 'Heavy-duty pipe wrench',
                'total_stock' => 15,
                'remaining' => 6,
                'supplier' => 'Tacloban Tools Depot',
                'category' => 'Tools',
                'unit' => 'Piece',
            ],
            [
                'name' => 'Rubber Insulation Mat',
                'type' => 'non-consumable',
                'description' => 'Electrical safety insulation mat',
                'total_stock' => 10,
                'remaining' => 3,
                'supplier' => 'SafeGrid Supplies',
                'category' => 'Electric Mats',
                'unit' => 'Piece',
            ],
            [
                'name' => 'Teflon Tape',
                'type' => 'consumable',
                'description' => 'Seal tape for plumbing connections',
                'total_stock' => 60,
                'remaining' => 5,
                'supplier' => 'Leyte Industrial Supply',
                'category' => 'Disposable',
                'unit' => 'Pack',
            ],
            [
                'name' => 'Adjustable Spanner 12in',
                'type' => 'non-consumable',
                'description' => 'Adjustable spanner for repair works',
                'total_stock' => 20,
                'remaining' => 9,
                'supplier' => 'Tacloban Tools Depot',
                'category' => 'Tools',
                'unit' => 'Piece',
            ],
        ];

        foreach ($items as $item) {
            $uuid = (string) Str::uuid();

            DB::table('items')->insert([
                'id' => $uuid,
                'name' => $item['name'],
                'type' => $item['type'],
                'description' => $item['description'],
                'total_stock' => $item['total_stock'],
                'remaining' => $item['remaining'],
                'picture' => null,
                'supplier' => $item['supplier'],
                'category_id' => $categoryIds[$item['category']] ?? null,
                'unit_id' => $unitIds[$item['unit']] ?? null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $itemIds[$item['name']] = $uuid;
        }

        // -------------------------------------------------
        // INVENTORIES
        // -------------------------------------------------
        $inventories = [
            [
                'item' => 'Pipe Wrench 14in',
                'status' => 'available',
                'holder' => 'Main Stock Room',
                'received_date' => now()->subDays(40)->toDateString(),
                'date_assigned' => null,
                'due_date' => null,
                'notes' => 'Available for deployment',
            ],
            [
                'item' => 'Adjustable Spanner 12in',
                'status' => 'borrowed',
                'holder' => 'Engr. Dela Cruz',
                'received_date' => now()->subDays(35)->toDateString(),
                'date_assigned' => now()->subDays(3)->toDateString(),
                'due_date' => now()->addDays(4)->toDateString(),
                'notes' => 'Borrowed for repair works',
            ],
            [
                'item' => 'Rubber Insulation Mat',
                'status' => 'installation',
                'holder' => 'Generator Room',
                'received_date' => now()->subDays(25)->toDateString(),
                'date_assigned' => now()->subDays(10)->toDateString(),
                'due_date' => null,
                'notes' => 'Installed near panel board',
            ],
            [
                'item' => 'Generator Fanbelt B-54',
                'status' => 'inspection',
                'holder' => 'Mechanical Area',
                'received_date' => now()->subDays(15)->toDateString(),
                'date_assigned' => now()->subDays(2)->toDateString(),
                'due_date' => null,
                'notes' => 'For upcoming preventive maintenance',
            ],
            [
                'item' => 'Pipe Wrench 14in',
                'status' => 'issued',
                'holder' => 'Maintenance Team A',
                'received_date' => now()->subDays(28)->toDateString(),
                'date_assigned' => now()->subDays(7)->toDateString(),
                'due_date' => null,
                'notes' => 'Issued for ongoing plumbing works',
            ],
            [
                'item' => 'Rubber Insulation Mat',
                'status' => 'maintenance',
                'holder' => 'Electrical Section',
                'received_date' => now()->subDays(12)->toDateString(),
                'date_assigned' => now()->subDays(1)->toDateString(),
                'due_date' => null,
                'notes' => 'Under condition check',
            ],
        ];

        $inventoryIds = [];

        foreach ($inventories as $inventory) {
            $uuid = (string) Str::uuid();

            DB::table('inventories')->insert([
                'id' => $uuid,
                'status' => $inventory['status'],
                'holder' => $inventory['holder'],
                'received_date' => $inventory['received_date'],
                'date_assigned' => $inventory['date_assigned'],
                'due_date' => $inventory['due_date'],
                'notes' => $inventory['notes'],
                'item_id' => $itemIds[$inventory['item']] ?? null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $inventoryIds[] = $uuid;
        }

        // -------------------------------------------------
        // ITEM DISTRIBUTIONS
        // -------------------------------------------------
        DB::table('item_distributions')->insert([
            [
                'transaction_id' => (string) Str::uuid(),
                'type' => 'distributed',
                'quantity' => 20,
                'department_or_borrower' => 'Electrical Department',
                'distribution_date' => now()->subDays(8)->toDateString(),
                'due_date' => null,
                'returned_date' => null,
                'status' => 'completed',
                'item_id' => $itemIds['Electrical Tape'],
                'inventory_id' => null,
                'notes' => 'Used for electrical maintenance',
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'transaction_id' => (string) Str::uuid(),
                'type' => 'borrowed',
                'quantity' => 1,
                'department_or_borrower' => 'Engr. Dela Cruz',
                'distribution_date' => now()->subDays(3)->toDateString(),
                'due_date' => now()->addDays(4)->toDateString(),
                'returned_date' => null,
                'status' => 'borrowed',
                'item_id' => $itemIds['Pipe Wrench 14in'],
                'inventory_id' => $inventoryIds[1] ?? null,
                'notes' => 'For urgent repair work',
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'transaction_id' => (string) Str::uuid(),
                'type' => 'issued',
                'quantity' => 1,
                'department_or_borrower' => 'Maintenance Team A',
                'distribution_date' => now()->subDays(7)->toDateString(),
                'due_date' => null,
                'returned_date' => null,
                'status' => 'issued',
                'item_id' => $itemIds['Adjustable Spanner 12in'],
                'inventory_id' => $inventoryIds[4] ?? null,
                'notes' => 'Issued for daily maintenance',
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'transaction_id' => (string) Str::uuid(),
                'type' => 'borrowed',
                'quantity' => 1,
                'department_or_borrower' => 'Plumbing Section',
                'distribution_date' => now()->subDays(14)->toDateString(),
                'due_date' => now()->subDays(10)->toDateString(),
                'returned_date' => now()->subDays(9)->toDateString(),
                'status' => 'returned',
                'item_id' => $itemIds['Pipe Wrench 14in'],
                'inventory_id' => $inventoryIds[0] ?? null,
                'notes' => 'Returned in good condition',
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // -------------------------------------------------
        // SERVICE RECORDS
        // -------------------------------------------------
        DB::table('service_records')->insert([
            [
                'id' => (string) Str::uuid(),
                'type' => 'maintenance',
                'description' => 'Routine maintenance of adjustable spanner',
                'service_date' => now()->subDays(6)->toDateString(),
                'completed_date' => now()->subDays(5)->toDateString(),
                'technician' => 'Mark Rivera',
                'status' => 'completed',
                'picture' => null,
                'remarks' => 'Lubricated and cleaned',
                'item_id' => $itemIds['Adjustable Spanner 12in'],
                'inventory_id' => $inventoryIds[1] ?? null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'inspection',
                'description' => 'Inspection of rubber insulation mat',
                'service_date' => now()->subDays(2)->toDateString(),
                'completed_date' => null,
                'technician' => 'Ana Lopez',
                'status' => 'scheduled',
                'picture' => null,
                'remarks' => 'Pending checklist completion',
                'item_id' => $itemIds['Rubber Insulation Mat'],
                'inventory_id' => $inventoryIds[2] ?? null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'installation',
                'description' => 'Installed rubber insulation mat near main panel',
                'service_date' => now()->subDays(10)->toDateString(),
                'completed_date' => now()->subDays(10)->toDateString(),
                'technician' => 'Carlos Mendoza',
                'status' => 'completed',
                'picture' => null,
                'remarks' => 'Successfully installed',
                'item_id' => $itemIds['Rubber Insulation Mat'],
                'inventory_id' => $inventoryIds[2] ?? null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'maintenance',
                'description' => 'Repair of pipe wrench jaw alignment',
                'service_date' => now()->subDays(1)->toDateString(),
                'completed_date' => null,
                'technician' => 'Leo Santos',
                'status' => 'under repair',
                'picture' => null,
                'remarks' => 'Awaiting replacement part',
                'item_id' => $itemIds['Pipe Wrench 14in'],
                'inventory_id' => $inventoryIds[0] ?? null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // -------------------------------------------------
        // PURCHASE REQUESTS
        // -------------------------------------------------
        DB::table('purchase_request')->insert([
            [
                'request_date' => now()->subDays(12)->toDateString(),
                'status' => 'pending',
                'items' => json_encode([
                    [
                        'item_id' => $itemIds['Generator Fanbelt B-54'],
                        'item_name' => 'Generator Fanbelt B-54',
                        'quantity' => 4,
                        'unit' => 'Piece',
                        'description' => 'For generator preventive maintenance',
                    ],
                    [
                        'item_id' => $itemIds['Electrical Tape'],
                        'item_name' => 'Electrical Tape',
                        'quantity' => 10,
                        'unit' => 'Pack',
                        'description' => 'For electrical rewiring support',
                    ],
                ]),
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'request_date' => now()->subDays(10)->toDateString(),
                'status' => 'approved',
                'items' => json_encode([
                    [
                        'item_id' => $itemIds['Pipe Wrench 14in'],
                        'item_name' => 'Pipe Wrench 14in',
                        'quantity' => 2,
                        'unit' => 'Piece',
                        'description' => 'Additional tool for plumbing team',
                    ],
                    [
                        'item_id' => $itemIds['Teflon Tape'],
                        'item_name' => 'Teflon Tape',
                        'quantity' => 15,
                        'unit' => 'Pack',
                        'description' => 'For pipe connection sealing',
                    ],
                ]),
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'request_date' => now()->subDays(7)->toDateString(),
                'status' => 'ordered',
                'items' => json_encode([
                    [
                        'item_id' => $itemIds['Rubber Insulation Mat'],
                        'item_name' => 'Rubber Insulation Mat',
                        'quantity' => 2,
                        'unit' => 'Piece',
                        'description' => 'For additional safety installation',
                    ],
                    [
                        'item_id' => $itemIds['Electrical Tape'],
                        'item_name' => 'Electrical Tape',
                        'quantity' => 8,
                        'unit' => 'Pack',
                        'description' => 'For scheduled maintenance works',
                    ],
                ]),
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'request_date' => now()->subDays(5)->toDateString(),
                'status' => 'received',
                'items' => json_encode([
                    [
                        'item_id' => $itemIds['Adjustable Spanner 12in'],
                        'item_name' => 'Adjustable Spanner 12in',
                        'quantity' => 3,
                        'unit' => 'Piece',
                        'description' => 'Tools received for maintenance unit',
                    ],
                ]),
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'request_date' => now()->subDays(3)->toDateString(),
                'status' => 'rejected',
                'items' => json_encode([
                    [
                        'item_id' => $itemIds['Teflon Tape'],
                        'item_name' => 'Teflon Tape',
                        'quantity' => 30,
                        'unit' => 'Pack',
                        'description' => 'Excess quantity request',
                    ],
                ]),
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}