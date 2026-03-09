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
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name' => 'User One',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name' => 'User Two',
                'password' => Hash::make('password123'),
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
            'Disposable'    => 'Single-use or consumable items',
            'Plumbing Mats' => 'Protective mats used in plumbing work',
            'Tools'         => 'Tools and equipment',
            'Electric Mats' => 'Electrical mats and accessories',
        ];

        foreach ($categories as $name => $description) {
            Category::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }
    }
}
