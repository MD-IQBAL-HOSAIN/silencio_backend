<?php

namespace Database\Seeders;

use App\Models\GroceryType;
use Illuminate\Database\Seeder;

class GroceryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Fruite',
            'Vegatable',
            'Milk',
            'Dairy',
            'Pantry',
            'SeeFood',
            'Meat',
            'Bakery',
            'Beverages',
            'Frozen Food',
        ];

        foreach ($types as $type) {
            $existingType = GroceryType::where('name', $type)->first();

            if ($existingType) {
                $this->command?->info('Grocery type already seeded done !!');
                continue;
            }

            GroceryType::create([
                'name' => $type,
                'status' => 'active',
            ]);
        }
    }
}
