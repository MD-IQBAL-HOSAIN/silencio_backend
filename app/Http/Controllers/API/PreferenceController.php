<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PreferenceResource;
use App\Models\Preference;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreferenceController extends Controller
{
    /**
     * Retrieve preferences, optionally filtered by type.
     *
     * Query params:
     * - type: cuisine|dietary|allergies (optional)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:cuisine,dietary,allergies',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $query = Preference::query()->where('status', 'active')->latest();
            $perPage = (int) $request->input('per_page', 10);

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            $preferences = $query->paginate($perPage)->withQueryString();
            $formattedPreferences = PreferenceResource::collection($preferences->items())
                ->collection
                ->map(fn($preference) => $preference->toArray($request))
                ->values();

            return jsonResponse(
                true,
                'Preferences retrieved successfully',
                200,
                $formattedPreferences,
                true,
                $preferences
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving preferences',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Store or update authenticated user's marked preferences.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upsertUserPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'preference_ids' => 'required|array|min:1',
            'preference_ids.*' => 'required|integer|distinct|exists:preferences,id',
        ]);

        if ($validator->fails()) {
            return jsonErrorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        try {
            $user = auth('api')->user();

            if (!$user) {
                return jsonErrorResponse('Unauthorized', 401);
            }

            $preferenceIds = collect($request->input('preference_ids', []))
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            foreach ($preferenceIds as $preferenceId) {
                UserPreference::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'preference_id' => $preferenceId,
                    ],
                    []
                );
            }

            UserPreference::where('user_id', $user->id)
                ->whereNotIn('preference_id', $preferenceIds)
                ->delete();

            $selectedPreferences = Preference::whereIn('id', $preferenceIds)
                ->where('status', 'active')
                ->get();

            $formattedPreferences = PreferenceResource::collection($selectedPreferences)
                ->collection
                ->map(fn($preference) => $preference->toArray($request))
                ->values();

            return jsonResponse(
                true,
                'User preferences saved successfully',
                200,
                $formattedPreferences
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while saving user preferences',
                500,
                [$e->getMessage()]
            );
        }
    }

    /**
     * Retrieve authenticated user's selected preferences.
     *
     * Query params:
     * - type: cuisine|dietary|allergies (optional)
     * - per_page: 1-100 (optional, default 10)
     * - page: >=1 (optional)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:cuisine,dietary,allergies',
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

            $selectedPreferencesQuery = Preference::where('status', 'active')
                ->whereIn('id', function ($query) use ($user) {
                    $query->select('preference_id')
                        ->from('user_preferences')
                        ->where('user_id', $user->id);
                });

            if ($request->filled('type')) {
                $selectedPreferencesQuery->where('type', $request->type);
            }

            $selectedPreferences = $selectedPreferencesQuery
                ->latest()
                ->paginate($perPage)
                ->withQueryString();

            $formattedPreferences = PreferenceResource::collection($selectedPreferences->items())
                ->collection
                ->map(fn($preference) => $preference->toArray($request))
                ->values();

            return jsonResponse(
                true,
                'User preferences retrieved successfully',
                200,
                $formattedPreferences,
                true,
                $selectedPreferences
            );
        } catch (\Exception $e) {
            return jsonErrorResponse(
                'Something went wrong while retrieving user preferences',
                500,
                [$e->getMessage()]
            );
        }
    }
}
