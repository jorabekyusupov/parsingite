<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/xml', [\App\Http\Controllers\ParsingController::class, 'index']);
Route::get('/parsing', [\App\Http\Controllers\ParsingController::class, 'scrapping']);
Route::get('/update', [\App\Http\Controllers\ParsingController::class, 'updateSeller']);
Route::get('/seller', [\App\Http\Controllers\ParsingController::class, 'seller']);

