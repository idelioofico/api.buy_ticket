<?php

use App\Http\Controllers\CustomerControllerApi;
use App\Http\Controllers\EventControllerApi;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('consult', [CustomerControllerApi::class, 'consult']);
        Route::post('/register', [CustomerControllerApi::class, 'store']);
    });

    Route::prefix('events')->group(function () {
        Route::get('/', [EventControllerApi::class, 'index']);
        Route::get('{code}/details', [EventControllerApi::class, 'details']);
        Route::get('categories', [EventControllerApi::class, 'categories']);
        // Route::prefix('payments')->group(function () {
            Route::post('checkout_link', [PaymentController::class, 'checkout_link']);
        // });
    });
});
