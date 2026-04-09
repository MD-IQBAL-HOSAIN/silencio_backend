<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\GroceryTypeSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SystemSeeder::class,
            PageSeeder::class,
            FaqSeeder::class,
            PreferenceSeeder::class,
            GroceryTypeSeeder::class,
            MealSeeder::class,


        ]);
    }
}
