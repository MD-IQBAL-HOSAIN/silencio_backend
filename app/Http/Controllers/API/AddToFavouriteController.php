<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavouriteMealResource;
use App\Models\Meal;
use App\Models\UserFavoriteMeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddToFavouriteController extends Controller
{
    /**
     * Retrieve authenticated user's favorite meal list with pagination.
     *
     * Query params:
     * - per_page: 1-100 (optional, default 10)
     * - page: >=1 (optional)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function favoriteMealList(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

            $favoriteMeals = Meal::query()
                ->select('meals.*')
                ->join('user_favorite_meals', 'user_favorite_meals.meal_id', '=', 'meals.id')
                ->where('user_favorite_meals.user_id', $user->id)
                ->where('meals.status', 'active')
                ->orderByDesc('user_favorite_meals.created_at')
                ->paginate($perPage)
                ->withQueryString();

            $formattedMeals = FavouriteMealResource::collection($favoriteMeals->items())
                ->collection
                ->map(fn($meal) => $meal->toArray($request))
                ->values();

            return jsonResponse(
                true,
                'Favorite meals retrieved successfully',
                200,
                $formattedMeals,
                true,
                $favoriteMeals
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving favorite meals',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Toggle authenticated user's favorite meal.
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

            $favorite = UserFavoriteMeal::where('user_id', $user->id)
                ->where('meal_id', $meal->id)
                ->first();

            if ($favorite) {
                $favorite->delete();

                return jsonResponse(
                    true,
                    'Meal removed from favorite successfully',
                    200,
                    [
                        'meal_id' => $meal->id,
                        'is_favorite' => false,
                    ]
                );
            }

            UserFavoriteMeal::create([
                'user_id' => $user->id,
                'meal_id' => $meal->id,
            ]);

            return jsonResponse(
                true,
                'Meal added to favorite successfully',
                200,
                [
                    'meal_id' => $meal->id,
                    'is_favorite' => true,
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while toggling favorite meal',
                500,
                [$e->getMessage()]
            );
        }
    }
}
