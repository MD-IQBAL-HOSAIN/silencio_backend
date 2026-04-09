<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Interfaces\PreferenceServiceInterface;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PreferenceController extends Controller
{
    public function __construct(private readonly PreferenceServiceInterface $preferenceService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @SuppressWarnings("unused")
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = $this->preferenceService->getAllLatest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    $name = $data->name;
                    return $name;
                })
                ->addColumn('type', function ($data) {
                    $type = ucfirst($data->type);
                    return $type;
                })
                ->addColumn('status', function ($data) {
                    $backgroundColor  = $data->status == "active" ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == "active" ? '26px' : '2px';
                    return getStatusHTML($data, $backgroundColor, $sliderTranslateX);
                })

                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="' . route('preference.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                     <i class="mdi mdi-pencil"></i>
                                </a>
                                 <a href="' . route('preference.show', ['id' => $data->id]) . '" type="button" class="btn btn-warning fs-14 text-white edit-icn" title="show">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                 <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </div>';
                })

                ->rawColumns(['name', 'type', 'status', 'action'])
                ->make();
        }

        return view('backend.layout.preferences.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response     *
     */
    public function create(): View
    {
        return view('backend.layout.preferences.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:preferences,name',
                'type' => 'required|in:cuisine,dietary,allergies',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->preferenceService->create($validator->validated());

            return redirect()->route('preference.index')->with('t-success', 'Created Successfully !!');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong!' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(int $id): View
    {
        $data = $this->preferenceService->findOrFail($id);
        return view('backend.layout.preferences.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:preferences,name,' . $id,
                'type' => 'required|in:cuisine,dietary,allergies',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->preferenceService->update($id, $validator->validated());

            return redirect()->route('preference.index')->with('t-success', 'Updated Successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong!');
        }
    }

    /**
     * Update the status of the specified dynamic page.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(int $id): JsonResponse
    {
        $data = $this->preferenceService->toggleStatus($id);
        if ($data->status === 'inactive') {

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data'    => $data,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Published Successfully.',
            'data'    => $data,
        ]);
    }

    /**
     * This function is used to show the details of a dynamic page.
     *
     * @param int $id The ID of the dynamic page to show.
     * @return \Illuminate\View\View
     */
    public function show(int $id): View
    {
        $data = $this->preferenceService->findOrFail($id);
        return view('backend.layout.preferences.show', compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->preferenceService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully.',
            ]);
        } catch (\Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the Data.',
            ]);
        }
    }
}
