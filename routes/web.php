<?php

use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\SpaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Single Page Application Entry
|--------------------------------------------------------------------------
|
| Any web request is routed to the Vue SPA shell so client-side routing
| provides 100% reload-free seamless navigation.
|
*/

Route::get('/medicines/search', [MedicineController::class, 'search']);
Route::get('/medicines/presets', [MedicineController::class, 'presets']);

Route::get('/{any}', [SpaController::class, 'index'])->where('any', '.*');
