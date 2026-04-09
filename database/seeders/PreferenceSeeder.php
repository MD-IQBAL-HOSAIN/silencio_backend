<?php

namespace Database\Seeders;

use App\Models\Preference;
use Illuminate\Database\Seeder;

class PreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preferences = [
            ['name' => 'Vegan', 'type' => 'dietary'],
            ['name' => 'Vegetarian', 'type' => 'dietary'],
            ['name' => 'Keto', 'type' => 'dietary'],
            ['name' => 'High Protein', 'type' => 'dietary'],
            ['name' => 'Gluten Free', 'type' => 'dietary'],

            ['name' => 'Italian', 'type' => 'cuisine'],
            ['name' => 'Mexican', 'type' => 'cuisine'],
            ['name' => 'Asian', 'type' => 'cuisine'],
            ['name' => 'Mediterranean', 'type' => 'cuisine'],
            ['name' => 'Indian', 'type' => 'cuisine'],

            ['name' => 'Nuts', 'type' => 'allergies'],
            ['name' => 'Dairy', 'type' => 'allergies'],
            ['name' => 'Eggs', 'type' => 'allergies'],
            ['name' => 'Shellfish', 'type' => 'allergies'],
            ['name' => 'Soy', 'type' => 'allergies'],
            ['name' => 'Gluten', 'type' => 'allergies'],
        ];

        foreach ($preferences as $preference) {
            $existingPreference = Preference::where('name', $preference['name'])->first();

            if ($existingPreference) {
                $this->command?->info('Preference already seeded done !!');
                continue;
            }

            Preference::create($preference);
        }
    }
}
