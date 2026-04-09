<?php

namespace App\Http\Controllers\Web\Backend;

use App\Interfaces\FaqServiceInterface;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    public function __construct(private readonly FaqServiceInterface $faqService)
    {
    }

    /**
     * Display a listing of content.
     *
     * @param Request $request
     * @return View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = $this->faqService->getAllLatest();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('question', function ($data) {
                    $question = $data->question ?? 'N/A';
                    return $question;
                })
                ->addColumn('answer', function ($data) {
                    $answer = Str::limit($data->answer ?? 'N/A', 30) . '...';
                    return $answer;
                })

                ->addColumn('status', function ($data) {
                    $backgroundColor  = $data->status == "active" ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == "active" ? '26px' : '2px';
                    return getStatusHTML($data, $backgroundColor, $sliderTranslateX);
                })

                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="' . route('faq.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                   <i class="mdi mdi-pencil"></i>
                                </a>
                                <a href="' . route('faq.show', ['id' => $data->id]) . '" type="button" class="btn btn-info fs-14 text-white edit-icn" title="Edit">
                                   <i class="mdi mdi-eye"></i>
                                </a>
                                 <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['question', 'answer', 'status', 'action'])
                ->make();
        }
        return view("backend.layout.faqs.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("backend.layout.faqs.create");
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
                'question' => 'required|string|max:500',
                'answer' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->faqService->create($validator->validated());

            return redirect()->route('faq.index')->with('t-success', 'Created Successfully !!');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong!' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): View
    {
        $data = $this->faqService->findOrFail($id);
        return view("backend.layout.faqs.edit", compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'nullable|string|max:500',
                'answer' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->faqService->update($id, $validator->validated());

            return redirect()->route('faq.index')->with('t-success', 'Updated Successfully!!');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong!' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */

    public function show(int $id): View
    {
        $data = $this->faqService->findOrFail($id);
        return view('backend.layout.faqs.show', compact('data'));
    }


    /**
     * Update the status of the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function status(int $id): JsonResponse
    {
        $data = $this->faqService->toggleStatus($id);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->faqService->delete($id);

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
