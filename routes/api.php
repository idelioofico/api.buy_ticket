<?php

use App\Http\Controllers\CustomerControllerApi;
use App\Http\Controllers\EventControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::post('/tokens/create', function (Request $request) {
//     $token = $request->user()->createToken($request->token_name);

//     return ['token' => $token->plainTextToken];
// });


Route::prefix('v1')->group(function () {


    Route::prefix('users')->group(function () {
        Route::get('consult', [CustomerControllerApi::class, 'consult']);
        Route::post('/register', [CustomerControllerApi::class, 'store']);
    });

    Route::prefix('events')->group(function () {
        Route::get('/',[EventControllerApi::class,'index']);
        Route::get('categories', [EventControllerApi::class, 'categories']);
    });

});


