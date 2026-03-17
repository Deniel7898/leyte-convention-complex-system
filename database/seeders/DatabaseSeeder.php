<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Units;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ----------------------
        // USERS
        // ----------------------
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'last_name' => 'Administrator',
                'first_name' => 'System',
                'middle_name' => 'A',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => true
            ]
        );

        User::updateOrCreate(
            ['email' => 'user1@example.com'],
            [
                'last_name' => 'Doe',
                'first_name' => 'John',
                'middle_name' => 'D',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'status' => true
            ]
        );

        User::updateOrCreate(
            ['email' => 'user2@example.com'],
            [
                'last_name' => 'Smith',
                'first_name' => 'Jane',
                'middle_name' => 'S',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'status' => true
            ]
        );

        // ----------------------
        // UNITS
        // ----------------------
        $units = [
            ['name' => 'Piece', 'abbr' => 'pc'],
            ['name' => 'Pack', 'abbr' => 'pk'],
            ['name' => 'Box', 'abbr' => 'bx'],
            ['name' => 'Set', 'abbr' => 'st'],
            ['name' => 'Liter', 'abbr' => 'L'],
            ['name' => 'Kilogram', 'abbr' => 'kg'],
        ];

        foreach ($units as $unit) {
            Units::updateOrCreate(
                ['name' => $unit['name']],
                [
                    'description' => $unit['name'] . ' unit',
                    'abbreviation' => $unit['abbr'],
                ]
            );
        }
        // ----------------------
        // CATEGORIES
        // ----------------------
        $categories = [
            'Disposable' => [
                'description' => 'Single-use or consumable items',
                'type' => 'consumable'
            ],
            'Plumbing Mats' => [
                'description' => 'Protective mats used in plumbing work',
                'type' => 'non-consumable'
            ],
            'Tools' => [
                'description' => 'Tools and equipment',
                'type' => 'non-consumable'
            ],
            'Electric Mats' => [
                'description' => 'Electrical mats and accessories',
                'type' => 'non-consumable'
            ],
            'Generator Fanbelt' => [
                'description' => 'Fanbelt used for generator maintenance and replacement',
                'type' => 'consumable'
            ],
        ];

        foreach ($categories as $name => $data) {
            Category::updateOrCreate(
                ['name' => $name],
                [
                    'description' => $data['description'],
                    'type' => $data['type']
                ]
            );
        }
    }
}
