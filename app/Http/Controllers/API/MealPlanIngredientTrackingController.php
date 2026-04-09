<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\MealPlan;
use App\Models\MealPlanIngredientTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MealPlanIngredientTrackingController extends Controller
{
    /**
     * Get date-wise ingredient checklist for authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIngredientsByDate(Request $request)
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

            $items = MealPlanIngredientTracking::query()
                ->with([
                    'ingredient:id,grocery_type_id,title,quantity',
                    'ingredient.groceryType:id,name',
                ])
                ->where('user_id', $user->id)
                ->whereDate('plan_date', $request->plan_date)
                ->orderBy('id')
                ->get();

            return jsonResponse(
                true,
                'Ingredient checklist fetched successfully',
                200,
                [
                    'plan_date' => $request->plan_date,
                    'total_items' => $items->count(),
                    'purchased_count' => $items->where('is_purchased', true)->count(),
                    'remaining_count' => $items->where('is_purchased', false)->count(),
                    'items' => $items->map(fn($row) => [
                        'id' => $row->id,
                        'meal_plan_id' => $row->meal_plan_id,
                        'ingredient_id' => $row->ingredient_id,
                        'title' => optional($row->ingredient)->title,
                        'quantity' => optional($row->ingredient)->quantity,
                        'grocery_type' => optional(optional($row->ingredient)->groceryType)->name,
                        'is_purchased' => $row->is_purchased,
                    ])->values(),
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while fetching ingredient checklist',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Store or update date-wise ingredient checklist for authenticated user.
     *
     * Rules:
     * - plan_date is required.
     * - ingredient_id must belong to meals included in the user's plan date.
     * - Records are upserted by user_id + plan_date + ingredient_id.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeIngredientsByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_date' => 'required|date',
            'ingredient_ids' => 'required|array|min:1',
            'ingredient_ids.*' => 'required|integer|distinct|exists:ingredients,id',
            'is_purchased' => 'nullable|boolean',
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

            $allowedIngredients = Ingredient::query()
                ->select(['id', 'meal_id', 'grocery_type_id', 'title', 'quantity'])
                ->with('groceryType:id,name')
                ->whereIn('meal_id', $uniqueMealIds->all())
                ->get()
                ->keyBy('id');

            $incomingIngredientIds = collect($request->input('ingredient_ids', []))
                ->unique()
                ->values();

            $invalidIds = $incomingIngredientIds
                ->reject(fn($id) => $allowedIngredients->has($id))
                ->values();

            if ($invalidIds->isNotEmpty()) {
                $details = [];

                foreach ($request->input('ingredient_ids', []) as $index => $id) {
                    if ($invalidIds->contains($id)) {
                        $details["ingredient_ids.$index"] = [
                            "ingredient_id: $id is not available in selected date meal plan",
                        ];
                    }
                }

                return jsonErrorResponse(
                    'Some ingredients are not found in selected date meal plan',
                    422,
                    [
                        'ingredient_id' => $invalidIds->all(),
                        'details' => $details,
                    ]
                );
            }

            $purchaseStatus = (bool) $request->input('is_purchased', true);

            $savedRows = DB::transaction(function () use ($request, $user, $plan, $incomingIngredientIds, $purchaseStatus) {
                $rows = collect();

                foreach ($incomingIngredientIds as $ingredientId) {
                    $ingredientId = (int) $ingredientId;

                    $row = MealPlanIngredientTracking::query()->updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'plan_date' => $request->plan_date,
                            'ingredient_id' => $ingredientId,
                        ],
                        [
                            'meal_plan_id' => $plan->id,
                            'is_purchased' => $purchaseStatus,
                        ]
                    );

                    $rows->push($row);
                }

                return $rows;
            });

            return jsonResponse(
                true,
                'Ingredient checklist saved successfully',
                200,
                [
                    'plan_date' => $request->plan_date,
                    'is_purchased' => $purchaseStatus,
                    'ingredient_ids' => $savedRows->pluck('ingredient_id')->values(),
                    'items' => $savedRows->map(fn($row) => [
                        'id' => $row->id,
                        'ingredient_id' => $row->ingredient_id,
                        'is_purchased' => $row->is_purchased,
                    ])->values(),
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while saving ingredient checklist',
                500,
                [$e->getMessage()]
            );
        }
    }
}
