<?php

use App\Http\Controllers\ApiController;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/login', [ApiController::class, 'login']); 
Route::post('/register', [ApiController::class, 'register']);

Route::get('/single-data/{id}', [ApiController::class, 'getSingleUserOperation']);// middleware icine alinacak
Route::delete('/operation-delete/{id}', [ApiController::class, 'operationDelete']);// middleware icine alinacak
Route::put('/operation-update/{id}', [ApiController::class, 'operationUpdate']);// middleware icine alinacak
Route::get('/rate', [ApiController::class, 'getRate']);// middleware icine alinacak
//Route::get('/deneme', [ApiController::class, 'allOperation']);// middleware icine alinacak

Route::middleware('auth:api')->group(function(){
    Route::get('/user-data', [ApiController::class, 'getUserData'])->name('user.data');

    Route::get('/logout', [ApiController::class, 'logout']); 
    Route::post('/add-operation', [ApiController::class, 'userOperationAdd']);
    Route::post('/authenticate', [ApiController::class, 'authenticate']);
    Route::get('/user-operation-data', [ApiController::class, 'getUserOperation']);
    Route::get('/total-filter', [ApiController::class, 'totalFilter']);
    Route::get('/date-filter', [ApiController::class, 'dateFilter']);
    Route::get('/category', [ApiController::class, 'getCategory']);
    Route::get('/currency', [ApiController::class, 'getCurrency']);
    
});