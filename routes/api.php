<?php

use Illuminate\Http\Request,
    Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController,
    App\Http\Controllers\ClientsController,
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

Route::group([
    'middleware' => ['api', 'cors']
], function ($router) {
    Route::controller(AuthController::class)
        ->group(function () {
            Route::get('verifyAccess', 'verifyUserAccess');
            Route::post('register', 'register');
            Route::post('login', 'login');
            Route::get('logout', 'logout');
        });

    // Sale Points
    Route::controller(SalePointsController::class)
        ->prefix('salePoints')
        ->group(function () {
            Route::get('/{idSalePoints?}', 'index');
            Route::get('toggle/{idSalePoints}', 'toggleActive');
            Route::post('', 'save');
        });

    // Clients
    Route::controller(ClientsController::class)
        ->prefix('clients')
        ->group(function () {
            Route::get('/{idClients?}', 'index');
            Route::get('toggle/{idClients}', 'toggleActive');
            Route::post('', 'save');
        });

    // Products
    Route::controller(ProductsController::class)
        ->prefix('products')
        ->group(function () {
            Route::get('/{idProducts?}', 'index');
            Route::get('toggle/{idProducts}', 'toggleActive');
            Route::post('', 'save');
        });

    // Sales
    Route::controller(SalesController::class)
        ->prefix('sales')
        ->group(function () {
            Route::get('/{idSales?}', 'index');
            Route::get('updateStatus/{idSales}/{status}', 'updateStatus');
            Route::post('', 'save');
        });
});
