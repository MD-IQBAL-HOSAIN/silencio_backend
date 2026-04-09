<?php

namespace Database\Seeders;

use App\Models\GroceryType;
use App\Models\Meal;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $meals = [
            [
                'meal' => [
                    'title' => 'Avocado Toast Delight',
                    'description' => 'Classic avocado toast with a gourmet twist, perfect for breakfast or brunch',
                    'time' => '15 min',
                    'servings' => '2',
                    'calories' => '320',
                    'protein' => '12g',
                    'carbs' => '35g',
                    'fats' => '18g',
                    'meal_type' => 'breakfast',
                    'status' => 'active',
                ],
                'ingredients' => [
                    ['title' => 'Sourdough bread', 'quantity' => '2 slices', 'grocery_type' => 'Bakery'],
                    ['title' => 'Avocado', 'quantity' => '1 ripe', 'grocery_type' => 'Fruite'],
                    ['title' => 'Eggs', 'quantity' => '2', 'grocery_type' => 'Dairy'],
                    ['title' => 'Cherry tomatoes', 'quantity' => '1/2 cup', 'grocery_type' => 'Vegatable'],
                    ['title' => 'Red pepper flakes', 'quantity' => '1 tsp', 'grocery_type' => 'Pantry'],
                    ['title' => 'Salt and pepper', 'quantity' => 'To taste', 'grocery_type' => 'Pantry'],
                ],
                'instructions' => [
                    ['title' => 'Toast bread until golden brown'],
                    ['title' => 'Mash avocado with salt, pepper, and lemon juice'],
                    ['title' => 'Fry or poach eggs to your liking'],
                    ['title' => 'Spread mashed avocado on toast'],
                    ['title' => 'Top with egg and cherry tomatoes'],
                    ['title' => 'Sprinkle with red pepper flakes and fresh herbs'],
                    ['title' => 'Serve immediately'],
                ],
            ],
            [
                'meal' => [
                    'title' => 'Grilled Chicken Bowl',
                    'description' => 'Balanced chicken bowl with fresh veggies and quinoa',
                    'time' => '25 min',
                    'servings' => '1',
                    'calories' => '480',
                    'protein' => '38g',
                    'carbs' => '42g',
                    'fats' => '16g',
                    'meal_type' => 'lunch',
                    'status' => 'active',
                ],
                'ingredients' => [
                    ['title' => 'Chicken breast', 'quantity' => '150g', 'grocery_type' => 'Meat'],
                    ['title' => 'Cooked quinoa', 'quantity' => '1 cup', 'grocery_type' => 'Pantry'],
                    ['title' => 'Cucumber slices', 'quantity' => '1/2 cup', 'grocery_type' => 'Vegatable'],
                    ['title' => 'Cherry tomatoes', 'quantity' => '1/2 cup', 'grocery_type' => 'Vegatable'],
                    ['title' => 'Olive oil dressing', 'quantity' => '2 tbsp', 'grocery_type' => 'Pantry'],
                ],
                'instructions' => [
                    ['title' => 'Season and grill chicken until cooked through'],
                    ['title' => 'Slice chicken into strips'],
                    ['title' => 'Arrange quinoa and vegetables in a bowl'],
                    ['title' => 'Top with grilled chicken'],
                    ['title' => 'Drizzle dressing and serve'],
                ],
            ],
        ];

        foreach ($meals as $mealData) {
            $existingMeal = Meal::where('title', $mealData['meal']['title'])->first();

            if ($existingMeal) {
                $this->command?->info('Meal already seeded done !!');
                continue;
            }

            $meal = Meal::create($mealData['meal']);

            $ingredients = collect($mealData['ingredients'])
                ->map(function (array $ingredient) {
                    $groceryType = GroceryType::firstOrCreate(
                        ['name' => $ingredient['grocery_type']],
                        ['status' => 'active']
                    );

                    return [
                        'title' => $ingredient['title'],
                        'quantity' => $ingredient['quantity'] ?? null,
                        'grocery_type_id' => $groceryType->id,
                    ];
                })
                ->values()
                ->all();

            $meal->ingredients()->createMany($ingredients);
            $meal->instructions()->createMany($mealData['instructions']);
        }
    }
}
