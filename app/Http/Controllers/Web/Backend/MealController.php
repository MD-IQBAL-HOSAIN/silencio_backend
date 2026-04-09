<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreMealRequest;
use App\Http\Requests\Backend\UpdateMealRequest;
use App\Models\GroceryType;
use App\Models\Meal;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MealController extends Controller
{
    /**
     * Display a listing of meals.
     *
     * @return View|JsonResponse
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Meal::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    $image = $data->image ? asset($data->image) : asset('frontend/no-image.jpg');
                    return '<img src="' . $image . '" width="60" alt=" Meal Image"/>';
                })
                ->addColumn('title', function ($data) {
                    return $data->title ?? 'N/A';
                })
                ->addColumn('meal_type', function ($data) {
                    return ucfirst($data->meal_type ?? 'N/A');
                })
                ->addColumn('time', function ($data) {
                    return $data->time ?? 'N/A';
                })
                ->addColumn('status', function ($data) {
                    $backgroundColor = $data->status == 'active' ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == 'active' ? '26px' : '2px';
                    return getStatusHTML($data, $backgroundColor, $sliderTranslateX);
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="' . route('meals.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                     <i class="mdi mdi-pencil"></i>
                                </a>
                                 <a href="' . route('meals.show', ['id' => $data->id]) . '" type="button" class="btn btn-warning fs-14 text-white edit-icn" title="Show">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                 <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['image', 'title', 'meal_type', 'time', 'status', 'action'])
                ->make();
        }

        return view('backend.layout.meals.index');
    }

    /**
     * Show the form for creating a new meal.
     */
    public function create(): View
    {
        $groceryTypes = GroceryType::where('status', 'active')->orderBy('name')->get();
        return view('backend.layout.meals.create', compact('groceryTypes'));
    }

    /**
     * Store a newly created meal.
     */
    public function store(StoreMealRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $data = new Meal();
                $data->title = $request->title;
                $data->description = $request->description;
                $data->time = $request->time;
                $data->servings = $request->servings;
                $data->calories = $request->calories;
                $data->protein = $request->protein;
                $data->carbs = $request->carbs;
                $data->fats = $request->fats;
                $data->meal_type = $request->meal_type;
                $data->status = 'active';

                if ($request->hasFile('image')) {
                    $imagePath = fileUpload_old(
                        $request->file('image'),
                        'meals',
                        time() . '_' . $request->file('image')->getClientOriginalName()
                    );

                    if ($imagePath !== null) {
                        $data->image = $imagePath;
                    }
                }

                $data->save();

                $ingredients = $this->sanitizeIngredients($request->input('ingredients', []));
                if (!empty($ingredients)) {
                    $data->ingredients()->createMany($ingredients);
                }

                $instructions = $this->sanitizeList($request->input('instructions', []));
                if (!empty($instructions)) {
                    $data->instructions()->createMany(
                        collect($instructions)->map(fn($item) => ['title' => $item])->all()
                    );
                }
            });

            return redirect()->route('meals.index')->with('t-success', 'Created Successfully !!');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified meal.
     */
    public function edit(int $id): View
    {
        $data = Meal::with(['ingredients.groceryType', 'instructions'])->findOrFail($id);
        $groceryTypes = GroceryType::where('status', 'active')->orderBy('name')->get();
        return view('backend.layout.meals.edit', compact('data', 'groceryTypes'));
    }

    /**
     * Update the specified meal.
     */
    public function update(UpdateMealRequest $request, int $id): RedirectResponse
    {
        try {
            $data = Meal::findOrFail($id);

            DB::transaction(function () use ($request, $data) {
                $data->title = $request->title;
                $data->description = $request->description;
                $data->time = $request->time;
                $data->servings = $request->servings;
                $data->calories = $request->calories;
                $data->protein = $request->protein;
                $data->carbs = $request->carbs;
                $data->fats = $request->fats;
                $data->meal_type = $request->meal_type;

                if ($request->hasFile('image')) {
                    if ($data->image && file_exists(public_path($data->image))) {
                        fileDelete(public_path($data->image));
                    }

                    $imagePath = fileUpload_old(
                        $request->file('image'),
                        'meals',
                        time() . '_' . $request->file('image')->getClientOriginalName()
                    );

                    if ($imagePath !== null) {
                        $data->image = $imagePath;
                    }
                }

                $data->save();

                $data->ingredients()->delete();
                $data->instructions()->delete();

                $ingredients = $this->sanitizeIngredients($request->input('ingredients', []));
                if (!empty($ingredients)) {
                    $data->ingredients()->createMany($ingredients);
                }

                $instructions = $this->sanitizeList($request->input('instructions', []));
                if (!empty($instructions)) {
                    $data->instructions()->createMany(
                        collect($instructions)->map(fn($item) => ['title' => $item])->all()
                    );
                }
            });

            return redirect()->route('meals.index')->with('t-success', 'Updated Successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    /**
     * Update status of meal.
     */
    public function status(int $id): JsonResponse
    {
        $data = Meal::findOrFail($id);
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();

        if ($data->status === 'inactive') {
            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $data,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Published Successfully.',
            'data' => $data,
        ]);
    }

    /**
     * Display the specified meal.
     */
    public function show(int $id): View
    {
        $data = Meal::with(['ingredients.groceryType', 'instructions'])->findOrFail($id);
        return view('backend.layout.meals.show', compact('data'));
    }

    /**
     * Remove the specified meal.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $meal = Meal::findOrFail($id);

            if ($meal->image && file_exists(public_path($meal->image))) {
                fileDelete(public_path($meal->image));
            }

            $meal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully.',
            ]);
        } catch (Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the Data.',
            ]);
        }
    }

    /**
     * Sanitize list input by trimming and removing empty values.
     *
     * @param array<int, mixed>|null $items
     * @return array<int, string>
     */
    private function sanitizeList(?array $items): array
    {
        if (!$items) {
            return [];
        }

        return collect($items)
            ->map(fn($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Sanitize ingredient rows (grocery_type_id + title + quantity).
     *
     * @param array<int, mixed>|null $items
     * @return array<int, array<string, mixed>>
     */
    private function sanitizeIngredients(?array $items): array
    {
        if (!$items) {
            return [];
        }

        return collect($items)
            ->map(function ($item) {
                $groceryTypeId = isset($item['grocery_type_id']) ? (int) $item['grocery_type_id'] : null;
                $title = isset($item['title']) ? trim((string) $item['title']) : '';
                $quantity = isset($item['quantity']) ? trim((string) $item['quantity']) : null;

                if (!$groceryTypeId || $title === '') {
                    return null;
                }

                return [
                    'grocery_type_id' => $groceryTypeId,
                    'title' => $title,
                    'quantity' => $quantity ?: null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
