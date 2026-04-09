<?php

use Illuminate\Support\Facades\Route;

/* Route::get('/{page?}', function ($page = null) {
    return redirect()->route('dashboard.index');
})->where('page', 'home|index');
 */

Route::get('/', function () {
    return redirect('/login');
});

require_once __DIR__ .'/auth.php';


