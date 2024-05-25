<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\PropertiesController;
use App\Http\Middleware\CheckForAdminOps;
use App\Http\Middleware\PlansMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Http\Middleware\Check;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/user', [AuthController::class, 'returUserResponse'])->middleware('auth:api')->name('user');

});



Route::group([
    //apply middleware such that only authenticated users are allowed to access
    'middleware' => ['auth:api', PlansMiddleware::class],
    'prefix' => 'properties'
], function ($router) {
    Route::get('/all', [PropertiesController::class, 'index']);
    Route::post('/createViaCsv', [PropertiesController::class, 'storeDataFromCSV']);
    Route::post('/create', [PropertiesController::class, 'storeDataViaRequest']);
    Route::get('/getConditionally', [PropertiesController::class, 'retrievePropertyConditionally']);
    Route::delete('delete/{id}', [PropertiesController::class, 'delete']);
    Route::post('/update/{id}', [PropertiesController::class, 'update']);
});

// apply auth and checkforadminops middleware and make routes from plancontroller

Route::group([
    'middleware' => ['auth:api', CheckForAdminOps::class],
    'prefix' => 'plans'
], function ($router) {
    Route::get('/all', [PlansController::class, 'index'])->withoutmiddleware(CheckForAdminOps::class);
    Route::post('/create', [PlansController::class, 'store']);
    Route::get('/show/{id}', [PlansController::class, 'show'])->withoutmiddleware(CheckForAdminOps::class);
    Route::post('/update/{id}', [PlansController::class, 'update']);
    Route::delete('/delete/{id}', [PlansController::class, 'destroy']);
    Route::post('/activate', [PlansController::class, 'activate']);
    Route::post('/deactivate', [PlansController::class, 'deactivate']);
});


