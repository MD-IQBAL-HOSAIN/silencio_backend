<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class LogoController extends Controller
{
    public function index()
    {
        try {
            $setting = Setting::query()->latest('id')->first();

            return jsonResponse(
                true,
                'Logo retrieved successfully',
                200,
                [
                    'logo' => $setting?->logo,
                ]
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving logo',
                500,
                [$e->getMessage()]
            );
        }
    }
}
