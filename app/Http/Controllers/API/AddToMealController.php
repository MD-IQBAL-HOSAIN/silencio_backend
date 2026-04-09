<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MealResource;
use App\Models\AddUserMeal;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddToMealController extends Controller
{
    /**
     * Retrieve authenticated user's added meal list with pagination.
     *
     * Query params:
    * - meal_type: lunch|dinner|breakfast (optional)
     * - per_page: 1-100 (optional, default 10)
     * - page: >=1 (optional)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mealList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meal_type' => 'nullable|in:lunch,dinner,breakfast',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $perPage = (int) $request->input('per_page', 10);

            $addedMeals = Meal::query()
                ->select('meals.*', 'add_user_meals.id as add_user_meal_id')
                ->join('add_user_meals', 'add_user_meals.meal_id', '=', 'meals.id')
                ->where('add_user_meals.user_id', $user->id)
                ->where('meals.status', 'active')
                ->when($request->filled('meal_type'), function ($query) use ($request) {
                    $query->where('meals.meal_type', $request->meal_type);
                })
                ->orderByDesc('add_user_meals.created_at')
                ->paginate($perPage)
                ->withQueryString();

            $formattedMeals = MealResource::collection($addedMeals->items())
                ->collection
                ->map(fn($meal) => array_merge($meal->toArray($request), [
                    'add_user_meal_id' => $meal->add_user_meal_id,
                    'is_added_to_meal' => true,
                ]))
                ->values();

            return jsonResponse(
                true,
                'Added meals retrieved successfully',
                200,
                $formattedMeals,
                true,
                $addedMeals
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving added meals',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Search authenticated user's added meals by title with pagination.
     *
     * Query params:
     * - title: search keyword (required)
     * - meal_type: lunch|dinner|breakfast (optional)
     * - per_page: 1-100 (optional, default 10)
     * - page: >=1 (optional)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchAddedMeals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:1|max:255',
            'meal_type' => 'nullable|in:lunch,dinner,breakfast',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $perPage = (int) $request->input('per_page', 10);
            $title = trim($request->input('title'));

            $addedMeals = Meal::query()
                ->select('meals.*', 'add_user_meals.id as add_user_meal_id')
                ->join('add_user_meals', 'add_user_meals.meal_id', '=', 'meals.id')
                ->where('add_user_meals.user_id', $user->id)
                ->where('meals.status', 'active')
                ->where('meals.title', 'like', '%' . $title . '%')
                ->when($request->filled('meal_type'), function ($query) use ($request) {
                    $query->where('meals.meal_type', $request->meal_type);
                })
                ->orderByDesc('add_user_meals.created_at')
                ->paginate($perPage)
                ->withQueryString();

            $formattedMeals = MealResource::collection($addedMeals->items())
                ->collection
                ->map(fn($meal) => array_merge($meal->toArray($request), [
                    'add_user_meal_id' => $meal->add_user_meal_id,
                    'is_added_to_meal' => true,
                ]))
                ->values();

            return jsonResponse(
                true,
                'Added meals retrieved successfully',
                200,
                $formattedMeals,
                true,
                $addedMeals
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while searching added meals',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Add a meal to authenticated user's meal list.
     * If the meal is already added, returns an informative message.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addMeal(int $id)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $meal = Meal::query()
                ->where('status', 'active')
                ->find($id);

            if (!$meal) {
                return jsonErrorResponse('Meal not found', 404);
            }

            $addedMeal = AddUserMeal::firstOrCreate([
                'user_id' => $user->id,
                'meal_id' => $meal->id,
            ]);

            return jsonResponse(
                true,
                $addedMeal->wasRecentlyCreated
                    ? 'Meal added to your meal list successfully'
                    : 'Meal already added to your meal list',
                200,
                [
                    'add_user_meal_id' => $addedMeal->id,
                    'meal_id' => $meal->id,
                    'is_added_to_meal' => true,
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while adding meal to your list',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Toggle authenticated user's add-to-meal item.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleMeal(int $id)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $meal = Meal::query()
                ->where('status', 'active')
                ->find($id);

            if (!$meal) {
                return jsonErrorResponse('Meal not found', 404);
            }

            $addedMeal = AddUserMeal::query()
                ->where('user_id', $user->id)
                ->where('meal_id', $meal->id)
                ->first();

            if ($addedMeal) {
                $addedMeal->delete();

                return jsonResponse(
                    true,
                    'Meal removed from your meal list successfully',
                    200,
                    [
                        'add_user_meal_id' => $addedMeal->id,
                        'meal_id' => $meal->id,
                        'is_added_to_meal' => false,
                    ]
                );
            }

            $newAddedMeal = AddUserMeal::create([
                'user_id' => $user->id,
                'meal_id' => $meal->id,
            ]);

            return jsonResponse(
                true,
                'Meal added to your meal list successfully',
                200,
                [
                    'add_user_meal_id' => $newAddedMeal->id,
                    'meal_id' => $meal->id,
                    'is_added_to_meal' => true,
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while toggling meal in your list',
                500,
                [$e->getMessage()]
            );
        }
    }
}
