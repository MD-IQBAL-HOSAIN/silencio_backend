<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'description' => 'nullable|string',
            'time' => 'nullable|string|max:100',
            'servings' => 'nullable|string|max:100',
            'calories' => 'nullable|string|max:100',
            'protein' => 'nullable|string|max:100',
            'carbs' => 'nullable|string|max:100',
            'fats' => 'nullable|string|max:100',
            'meal_type' => 'required|in:lunch,dinner,breakfast',
            'ingredients' => 'nullable|array',
            'ingredients.*.grocery_type_id' => 'required|integer|exists:grocery_types,id',
            'ingredients.*.title' => 'required|string|max:255',
            'ingredients.*.quantity' => 'nullable|string|max:255',
            'instructions' => 'nullable|array',
            'instructions.*' => 'nullable|string|max:255',
        ];
    }
}
