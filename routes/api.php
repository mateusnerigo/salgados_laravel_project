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

$routeCommomGroup = function() {
    Route::get('', 'index');
    Route::post('', 'save');
};

// Sale Points
Route::controller(SalePointsController::class)
    ->prefix('salePoints')
    ->group($routeCommomGroup);

// Clients
Route::controller(ClientsController::class)
    ->prefix('clients')
    ->group($routeCommomGroup);
