<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\MessageController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->get('/tickets', [TicketController::class, 'index']);
Route::middleware('auth:sanctum')->get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::middleware('auth:sanctum')->post('/tickets', [TicketController::class, 'store']);
Route::middleware('auth:sanctum')->put('/tickets/{ticket}', [TicketController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/tickets/{id}', [TicketController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/tickets/{ticketId}/messages', [MessageController::class, 'index']);
Route::middleware('auth:sanctum')->post('/tickets/{ticketId}/messages', [MessageController::class, 'store']);