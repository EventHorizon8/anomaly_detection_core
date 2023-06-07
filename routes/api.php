<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/account', function (Request $request) {
    return $request->user();
})->name('account');

Route::middleware('auth:sanctum')->delete(
    '/authorise',
    [\App\Http\Controllers\LogoutController::class, 'logoutUser'],
)->name('logout');


Route::post(
    '/authorise',
    [\App\Http\Controllers\LoginController::class, 'authoriseUser'],
)->name('authorise');

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::apiResources([
        'clients' => \App\Http\Controllers\ClientController::class,
    ]);
});
