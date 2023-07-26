<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\PreuveTransactionController;
use App\Http\Controllers\StatutController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\SettingsController;

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

Route::prefix('users')->group(function () {
    Route::post('', [UserController::class, 'create']);
    Route::get('{id}',[UserController::class, 'get']);
    Route::get('',[UserController::class,'getAll']);
    Route::put('{id}',[UserController::class,'update']);
    Route::delete('{id}',[UserController::class,'delete']);
    Route::post('login',[UserController::class, 'Login']);
    Route::get('command/{id}',[UserController::class, 'getCommandUser']);
});

Route::prefix('clients')->group(function () {
    Route::post('', [ClientController::class, 'create']);
    Route::get('{id}',[ClientController::class, 'get']);
    Route::get('',[ClientController::class,'getAll']);
    Route::put('{id}',[ClientController::class,'update']);
    Route::delete('{id}',[ClientController::class,'delete']);
});

Route::prefix('commandes')->group(function () {
    Route::post('', [CommandeController::class, 'create']);
    Route::get('{id}',[CommandeController::class, 'get']);
    Route::get('',[CommandeController::class,'getAll']);
    Route::put('{id}',[CommandeController::class,'update']);
    Route::delete('{id}',[CommandeController::class,'delete']);
});

Route::prefix('preuvetransaction')->group(function () {
    Route::post('', [PreuveTransactionController::class, 'create']);
    Route::get('{id}',[PreuveTransactionController::class, 'get']);
    Route::get('',[PreuveTransactionController::class,'getAll']);
    Route::put('{id}',[PreuveTransactionController::class,'update']);
    Route::delete('{id}',[PreuveTransactionController::class,'delete']);
});

Route::prefix('statuts')->group(function () {
    Route::post('', [StatutController::class, 'create']);
    Route::get('{id}',[StatutController::class, 'get']);
    Route::get('',[StatutController::class,'getAll']);
    Route::put('{id}',[StatutController::class,'update']);
    Route::delete('{id}',[StatutController::class,'delete']);
});

Route::prefix('commissions')->group(function () {
    Route::post('', [CommissionController::class, 'create']);
    Route::get('{id}',[CommissionController::class, 'get']);
    Route::get('',[CommissionController::class,'getAll']);
    Route::put('{id}',[CommissionController::class,'update']);
    Route::delete('{id}',[CommissionController::class,'delete']);
});

Route::prefix('settings')->group(function () {
    Route::put('', [SettingsController::class, 'updateSettings']);
});

