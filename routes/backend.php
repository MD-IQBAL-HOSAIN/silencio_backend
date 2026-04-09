<?php

use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\DynamicPageController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\GroceryTypeController;
use App\Http\Controllers\Web\Backend\MealController;
use App\Http\Controllers\Web\Backend\PreferenceController;
use App\Http\Controllers\Web\Backend\SystemUserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
|  Without prefix route
|--------------------------------------------------------------------------
|
*/


Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// FAQ routes (resource CRUD + custom status action)
Route::patch('/faq/{id}/status', [FaqController::class, 'status'])->name('faq.status');
Route::resource('faq', FaqController::class)->parameters([
    'faq' => 'id',
]);

//! System User routes (resource CRUD + custom status action)
Route::post('system-user/status/{id}', [SystemUserController::class, 'status'])
    ->name('system-user.status');

    //! System User routes (resource CRUD + custom status action)
Route::resource('system-user', SystemUserController::class)
    ->except(['show']);

//! Dynamic Page routes (resource CRUD + custom status action)
Route::patch('/dynamic/{id}/status', [DynamicPageController::class, 'status'])->name('dynamic.status');
Route::resource('dynamic', DynamicPageController::class)->parameters([
    'dynamic' => 'id',
]);

//! Preference routes (resource CRUD + custom status action)
Route::patch('/preference/{id}/status', [PreferenceController::class, 'status'])->name('preference.status');
Route::resource('preference', PreferenceController::class)->parameters([
    'preference' => 'id',
])->names('preference');

//! Meal routes (resource CRUD)
Route::patch('/meals/{id}/status', [MealController::class, 'status'])->name('meals.status');
Route::resource('meals', MealController::class)->parameters([
    'meals' => 'id',
])->names('meals');

//! Grocery Type routes (resource CRUD)
Route::patch('/grocery-types/{id}/status', [GroceryTypeController::class, 'status'])->name('grocery-types.status');
Route::resource('grocery-types', GroceryTypeController::class)->parameters([
    'grocery-types' => 'id',
])->names('grocery-types');



require_once __DIR__ . '/queue.php';
require_once __DIR__ . '/settings.php';
