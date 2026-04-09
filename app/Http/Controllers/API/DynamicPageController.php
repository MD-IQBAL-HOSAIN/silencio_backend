<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Page;
use Illuminate\Http\Request;

class DynamicPageController extends Controller
{
/**
 * Retrieve all dynamic pages.
 *
 * @return \Illuminate\Http\JsonResponse
 */
   public function index()
    {
        try {
            $dynamicPage = Page::where('status', 'active')->latest()->get();

            return jsonResponse(
                true,
                'DynamicPage retrieved successfully',
                200,
                $dynamicPage
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving Dynamic Page',
                500,
                [$e->getMessage()]
            );
        }
    }
   public function faq()
    {
        try {
            $dynamicPage = Faq::where('status', 'active')->latest()->get();

            return jsonResponse(
                true,
                'FAQ retrieved successfully',
                200,
                $dynamicPage
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving FAQ',
                500,
                [$e->getMessage()]
            );
        }
    }
}
