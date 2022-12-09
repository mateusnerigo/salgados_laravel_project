<?php

use Illuminate\Http\Request,
    Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientsController,
    App\Http\Controllers\SalePointsController,
    App\Http\Controllers\SalesController,
    App\Http\Controllers\ProductsController;

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

// Sale Points
Route::controller(SalePointsController::class)
    ->prefix('salePoints')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'save');
        Route::post('/salePoint', 'show');
        Route::post('/toggle', 'toggleActive');
    });

// Clients
Route::controller(ClientsController::class)
    ->prefix('clients')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'save');
        Route::post('/client', 'show');
        Route::post('/toggle', 'toggleActive');
    });

// Products
Route::controller(ProductsController::class)
    ->prefix('products')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'save');
        Route::post('/product', 'show');
        Route::post('/toggle', 'toggleActive');
    });

// Sales
Route::controller(SalesController::class)
    ->prefix('sales')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('', 'index');
        Route::post('', 'save');
        Route::post('/sale', 'show');
        Route::post('/updateStatus', 'updateStatus');
    });
