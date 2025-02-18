<?php

use App\Http\Controllers\CustomerControllerApi;
use App\Http\Controllers\EventControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('consult', [CustomerControllerApi::class, 'consult']);
        Route::post('/register', [CustomerControllerApi::class, 'store']);
    });

    Route::prefix('events')->group(function () {
        Route::get('/',[EventControllerApi::class,'index']);
        Route::get('{code}',[EventControllerApi::class,'details']);
        Route::get('categories', [EventControllerApi::class, 'categories']);
    });

});
