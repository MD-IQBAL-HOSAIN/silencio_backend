<?php

namespace App\Http\Resources;

use App\Models\AddUserMeal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = auth('api')->user()?->id;
        $isAddedMeal = false;

        if ($userId) {
            $isAddedMeal = AddUserMeal::query()
                ->where('user_id', $userId)
                ->where('meal_id', $this->id)
                ->exists();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'description' => $this->description,
            'time' => $this->time,
            'servings' => $this->servings,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fats' => $this->fats,
            'meal_type' => $this->meal_type,
            'is_added_to_meal' => $isAddedMeal,
            'ingredients' => $this->whenLoaded('ingredients', function () {
                return $this->ingredients
                    ->groupBy(fn($ingredient) => $ingredient->groceryType->name ?? 'Others')
                    ->map(function ($ingredients) {
                        return $ingredients->map(fn($ingredient) => [
                            'title' => $ingredient->title,
                            'quantity' => $ingredient->quantity,
                        ])->values();
                    })
                    ->toArray();
            }, []),
            'instructions' => $this->whenLoaded('instructions', function () {
                return $this->instructions->map(fn($instruction) => [
                    'title' => $instruction->title,
                ])->values();
            }, []),
        ];
    }
}
