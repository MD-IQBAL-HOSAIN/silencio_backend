<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AddUserMeal;
use App\Models\Ingredient;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MealPlanController extends Controller
{
    /**
     * Retrieve date-wise ingredient summary for authenticated user's meal plan.
     *
     * Rules:
     * - Duplicate meal_id values in plan items are counted once for ingredients.
     * - Ingredients are grouped by grocery type name.
     *
     * Query params:
     * - plan_date: YYYY-MM-DD (required)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ingredientsByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $plan = MealPlan::query()
                ->with('items:id,meal_plan_id,meal_id')
                ->where('user_id', $user->id)
                ->whereDate('plan_date', $request->plan_date)
                ->first();

            if (!$plan) {
                return jsonErrorResponse('Meal plan not found for selected date', 404);
            }

            $uniqueMealIds = $plan->items
                ->pluck('meal_id')
                ->filter(fn($id) => !is_null($id))
                ->unique()
                ->values();

            if ($uniqueMealIds->isEmpty()) {
                return jsonResponse(
                    true,
                    'Meal plan ingredients retrieved successfully',
                    200,
                    [
                        'plan_date' => $plan->plan_date?->toDateString(),
                        'ingredients' => new \stdClass(),
                    ]
                );
            }

            $ingredients = Ingredient::query()
                ->select(['id', 'meal_id', 'grocery_type_id', 'title', 'quantity'])
                ->with('groceryType:id,name')
                ->whereIn('meal_id', $uniqueMealIds->all())
                ->get();

            $groupedIngredients = $ingredients
                ->groupBy(function ($ingredient) {
                    return $ingredient->groceryType?->name ?? 'Others';
                })
                ->map(function ($items) {
                    return $items->map(function ($ingredient) {
                        return [
                            'id' => $ingredient->id,
                            'title' => $ingredient->title,
                            'quantity' => $ingredient->quantity,
                        ];
                    })->values();
                });

            return jsonResponse(
                true,
                'Meal plan ingredients retrieved successfully',
                200,
                [
                    'plan_date' => $plan->plan_date?->toDateString(),
                    'ingredients' => $groupedIngredients,
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving meal ingredients',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Retrieve date-wise summary for authenticated user's meal plan.
     *
     * Summary fields:
     * - set_calories
     * - total_meal_count
     * - total_meal_calories
     *
     * Query params:
     * - plan_date: YYYY-MM-DD (required)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summaryByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $plan = MealPlan::query()
                ->with(['items:id,meal_plan_id,meal_id', 'items.meal:id,calories'])
                ->where('user_id', $user->id)
                ->whereDate('plan_date', $request->plan_date)
                ->first();

            if (!$plan) {
                return jsonErrorResponse('Meal plan not found for selected date', 404);
            }

            $mealItems = $plan->items->filter(fn($item) => !is_null($item->meal_id));

            $totalMealCount = $mealItems->count();
            $totalMealCalories = $mealItems->sum(function ($item) {
                return (int) ($item->meal->calories ?? 0);
            });

            return jsonResponse(
                true,
                'Meal plan summary retrieved successfully',
                200,
                [
                    'plan_date' => $plan->plan_date?->toDateString(),
                    'set_calories' => $plan->set_calories,
                    'total_meal_count' => $totalMealCount,
                    'total_meal_calories' => $totalMealCalories,
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving meal plan summary',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Retrieve authenticated user's meal plan for a specific date.
     *
     * Query params:
     * - plan_date: YYYY-MM-DD (required)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $plan = MealPlan::query()
                ->with(['items:id,meal_plan_id,meal_name,meal_time,meal_id', 'items.meal:id,title,image'])
                ->where('user_id', $user->id)
                ->whereDate('plan_date', $request->plan_date)
                ->first();

            if (!$plan) {
                return jsonErrorResponse('Meal plan not found for selected date', 404);
            }

            return jsonResponse(
                true,
                'Meal plan retrieved successfully',
                200,
                [
                    'id' => $plan->id,
                    'user_id' => $plan->user_id,
                    'plan_date' => $plan->plan_date?->toDateString(),
                    'set_calories' => $plan->set_calories,
                    'items' => $plan->items->map(fn($item) => [
                        'id' => $item->id,
                        'meal_name' => $item->meal_name,
                        'meal_time' => $item->meal_time,
                        'meal_id' => $item->meal_id,
                        'meal_title' => $item->meal?->title,
                        'meal_image' => $item->meal?->image,
                    ])->values(),
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving meal plan',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Store or update daily meal plan for the authenticated user.
     *
     * Rules:
     * - One daily plan header per user/date.
     * - Items are dynamic (breakfast, lunch, evening, midnight, custom, etc.).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'set_calories' => 'nullable|integer|min:1',
            'plan_date'    => 'required|date',
            'items' => 'nullable|array',
            'items.*.meal_name' => 'required_with:items|string|max:100',
            'items.*.meal_time' => 'nullable|date_format:H:i',
            'items.*.meal_id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $user = auth('api')->user();

            if (! $user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $itemsPayload = collect($request->input('items', []))
                ->map(function ($item) {
                    return [
                        'meal_name' => isset($item['meal_name']) ? trim($item['meal_name']) : null,
                        'meal_time' => $item['meal_time'] ?? null,
                        'meal_id' => $item['meal_id'] ?? null,
                    ];
                })
                ->filter(fn($item) => !empty($item['meal_name']))
                ->values();

            $requestedMealIds = $itemsPayload
                ->pluck('meal_id')
                ->filter(fn($id) => !is_null($id))
                ->unique()
                ->values();

            if ($requestedMealIds->isNotEmpty()) {
                $allowedMealIds = AddUserMeal::query()
                    ->where('user_id', $user->id)
                    ->whereIn('meal_id', $requestedMealIds->all())
                    ->pluck('meal_id');

                $invalidMealIds = $requestedMealIds->diff($allowedMealIds)->values();

                if ($invalidMealIds->isNotEmpty()) {
                    return jsonErrorResponse(
                        'Some meals are not found in your added meal list',
                        422,
                        ['meal_id' => $invalidMealIds->all()]
                    );
                }
            }

            $plan = DB::transaction(function () use ($user, $request, $itemsPayload) {
                $plan = MealPlan::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'plan_date' => $request->plan_date,
                    ],
                    [
                        'set_calories' => $request->input('set_calories'),
                    ]
                );

                if ($request->exists('items')) {
                    MealPlanItem::query()->where('meal_plan_id', $plan->id)->delete();

                    foreach ($itemsPayload as $item) {
                        MealPlanItem::create([
                            'meal_plan_id' => $plan->id,
                            'meal_name' => $item['meal_name'],
                            'meal_time' => $item['meal_time'],
                            'meal_id' => $item['meal_id'],
                        ]);
                    }
                }

                return $plan->fresh('items');
            });

            return jsonResponse(
                true,
                'Meal plan saved successfully',
                200,
                [
                    'id' => $plan->id,
                    'user_id' => $plan->user_id,
                    'plan_date' => $plan->plan_date?->toDateString(),
                    'set_calories' => $plan->set_calories,
                    'items' => $plan->items->map(fn($item) => [
                        'id' => $item->id,
                        'meal_name' => $item->meal_name,
                        'meal_time' => $item->meal_time,
                        'meal_id' => $item->meal_id,
                    ])->values(),
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while saving meal plan',
                500,
                [$e->getMessage()]
            );
        }
    }
}
