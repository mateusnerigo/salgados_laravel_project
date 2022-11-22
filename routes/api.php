<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\SalePointsController;

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

$routeCommomGroup =

// Sale Points
Route::controller(SalePointsController::class)
    ->prefix('salePoints')
    ->group(function() {
        Route::get('', 'index');
        Route::post('', 'save');
        Route::post('/salePoint', 'show');
        Route::post('/toggle', 'toggleActive');
    });

// Clients
Route::controller(ClientsController::class)
    ->prefix('clients')
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'save');
        Route::post('/client', 'show');
        Route::post('/toggle', 'toggleActive');
    });
