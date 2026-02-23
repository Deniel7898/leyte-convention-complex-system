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
        $units = ['Piece', 'Pack', 'Box', 'Set', 'Liter', 'Kilogram'];

        foreach ($units as $unitName) {
            Units::updateOrCreate(
                ['name' => $unitName],
                ['description' => $unitName . ' unit']
            );
        }

        // ----------------------
        // CATEGORIES
        // ----------------------
        $categories = [
            'Stationery' => 'Items used for office work',
            'Electronics' => 'Electronic devices and accessories',
            'Furniture' => 'Office furniture items',
            'Cleaning Supplies' => 'Cleaning and janitorial items'
        ];

        foreach ($categories as $name => $description) {
            Category::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }
    }
}