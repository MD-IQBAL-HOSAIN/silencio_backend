<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AddToFavouriteController;
use App\Http\Controllers\API\LogoController;
use App\Http\Controllers\API\AddToMealController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FcmTokenController;
use App\Http\Controllers\API\DynamicPageController;
use App\Http\Controllers\API\MealController;
use App\Http\Controllers\API\MealPlanIngredientTrackingController;
use App\Http\Controllers\API\MealPlanController;
use App\Http\Controllers\API\PreferenceController;
use App\Http\Controllers\API\SocialLoginController;

/*
|--------------------------------------------------------------------------
| without jwt api middleware
|--------------------------------------------------------------------------
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/password/forgot', 'forgotPassword');
    Route::post('/password/reset', 'resetPassword');
    Route::post('/password/resend-otp', 'resendOtp');
    Route::post('/password/verify-otp', 'verifyOtp');
});

//Continue with google,facebook and apple login
Route::controller(SocialLoginController::class)->group(function () {
    Route::post('/social/login', 'socialLogin');
    Route::post('/guest/login', 'guestLogin');
});

// Route for preference list (supports optional ?type=cuisine|dietary|allergies)
Route::controller(PreferenceController::class)->group(function () {
    Route::get('/preferences', 'index');
});

// Route for meal list (supports optional ?meal_type=lunch|dinner|breakfast&per_page=10&page=1)
Route::controller(MealController::class)->group(function () {
    Route::get('/meals', 'index');
    Route::get('/meals/search', 'search');
    Route::get('/meals/{id}', 'show');
});

// Route for getting app logo from settings table
Route::controller(LogoController::class)->group(function () {
    Route::get('/logo', 'index');
});


/*
|--------------------------------------------------------------------------
| with jwt middlware api
|--------------------------------------------------------------------------
|
*/
// Throttle: max 60 requests per minute
Route::middleware('auth:api', 'throttle:60,1')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::post('/profile', 'profile');
        Route::delete('/delete-account', 'deleteAccount');
        Route::post('/profile/update/user', 'ProfileUpdate');
        Route::post('/password/update/user', 'ChangePassword');
        Route::get('/user/profile/get', 'profileRetrieval');
    });

    // Routes for Dynamic Page
    Route::controller(DynamicPageController::class)->group(function () {
        Route::get('dynamic-page', 'index');
        Route::get('faq-list', 'faq');
    });

    //Route for Fcm token store
    Route::controller(FcmTokenController::class)->group(function () {
        Route::post('/fcm/token/store', 'store');
        Route::delete('/fcm/token/delete/{id}', 'destroy');
    });

    // Route for storing/updating authenticated user's selected preferences
    Route::controller(PreferenceController::class)->group(function () {
        Route::get('/user/preferences', 'getUserPreferences');
        Route::post('/user/preferences', 'upsertUserPreferences');
    });

    // Route for add/remove authenticated user's favorite meal (toggle)
    Route::controller(AddToFavouriteController::class)->group(function () {
        Route::get('/user/favorite/meals', 'favoriteMealList');
        Route::post('/meals/{id}/favorite/toggle', 'toggleMeal');
    });

    // Route for authenticated user's add-to-meal in his wallet
    Route::controller(AddToMealController::class)->group(function () {
        Route::get('/user/added/meals', 'mealList');
        Route::get('/user/added/meals/search', 'searchAddedMeals');
        Route::post('/meals/{id}/add', 'addMeal');
        Route::post('/meals/{id}/add/toggle', 'toggleMeal');
    });

    // Route for storing authenticated user's meal plan.
    Route::controller(MealPlanController::class)->group(function () {
        Route::get('/meal-plans', 'showByDate');
        Route::get('/meal-plans/summary', 'summaryByDate');
        Route::get('/meal-plans/ingredients', 'ingredientsByDate');
        Route::post('/meal-plans', 'store');
    });

    // Route for storing/updating authenticated user's ingredient checklist.
    Route::controller(MealPlanIngredientTrackingController::class)->group(function () {
        Route::get('/meal-tracking/ingredients', 'getIngredientsByDate');
        Route::post('/meal-tracking/ingredients', 'storeIngredientsByDate');
    });






});


