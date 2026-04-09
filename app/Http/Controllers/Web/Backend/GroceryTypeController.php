<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\GroceryType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GroceryTypeController extends Controller
{
    public function __construct(private readonly GroceryType $groceryType)
    {
    }

    /**
     * Display a listing of grocery types.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = $this->groceryType->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->name ?? 'N/A';
                })
                ->addColumn('status', function ($data) {
                    $backgroundColor = $data->status == 'active' ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == 'active' ? '26px' : '2px';
                    return getStatusHTML($data, $backgroundColor, $sliderTranslateX);
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="' . route('grocery-types.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                     <i class="mdi mdi-pencil"></i>
                                </a>
                                 <a href="' . route('grocery-types.show', ['id' => $data->id]) . '" type="button" class="btn btn-warning fs-14 text-white edit-icn" title="Show">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                 <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['name', 'status', 'action'])
                ->make();
        }

        return view('backend.layout.grocery_types.index');
    }

    /**
     * Show the form for creating a new grocery type.
     */
    public function create(): View
    {
        return view('backend.layout.grocery_types.create');
    }

    /**
     * Store a newly created grocery type.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:grocery_types,name',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->groceryType->create([
                'name' => $request->name,
                'status' => 'active',
            ]);

            return redirect()->route('grocery-types.index')->with('t-success', 'Created Successfully !!');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified grocery type.
     */
    public function edit(int $id): View
    {
        $data = $this->groceryType->findOrFail($id);
        return view('backend.layout.grocery_types.edit', compact('data'));
    }

    /**
     * Update the specified grocery type.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:grocery_types,name,' . $id,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $this->groceryType->findOrFail($id);
            $data->name = $request->name;
            $data->save();

            return redirect()->route('grocery-types.index')->with('t-success', 'Updated Successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    /**
     * Toggle status of grocery type.
     */
    public function status(int $id): JsonResponse
    {
        $data = $this->groceryType->findOrFail($id);
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
     * Display the specified grocery type.
     */
    public function show(int $id): View
    {
        $data = $this->groceryType->findOrFail($id);
        return view('backend.layout.grocery_types.show', compact('data'));
    }

    /**
     * Remove the specified grocery type.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $data = $this->groceryType->findOrFail($id);
            $data->delete();

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
}
