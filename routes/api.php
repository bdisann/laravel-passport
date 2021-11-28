<?php

use App\Http\Controllers\AuthController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/** 
* Test API Route
* */
Route::get('v1/auth/test', function () {
    return response()->json([
        "status" => true,
        "message" => "OK"
    ]);
}); // End Test API Route


/**
 * Auth Routes
 * */ 
Route::post('v1/auth/login', [AuthController::class, 'login']); // Login Route
Route::post('v1/auth/register', [AuthController::class, 'register']); // Register Route
Route::post('v1/auth/request-forget-password', [AuthController::class, 'request_forget_password']); // Request Reset Password Route
