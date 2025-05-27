<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Reservation System API",
 *     description="Dokumentacioni i API-ve për sistemin e rezervimeve",
 *     @OA\Contact(
 *         name="Support",
 *         email="support@example.com"
 *     )
 * )
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationChangeRequestController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\AdminController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/tables', [TableController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);

    Route::post('/change-requests', [ReservationChangeRequestController::class, 'store']);
    Route::get('/change-requests', [ReservationChangeRequestController::class, 'index']);
    Route::get('/change-requests/my', [ReservationChangeRequestController::class, 'my']);
    Route::post('/change-requests/{id}/approve', [ReservationChangeRequestController::class, 'approve']);
    Route::post('/change-requests/{id}/reject', [ReservationChangeRequestController::class, 'reject']);
    Route::delete('/change-requests/{id}', [ReservationChangeRequestController::class, 'destroy']);

    Route::get('/admin/statistics', [AdminController::class, 'statistics']);
});

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});
Route::middleware('auth:sanctum')->get('/admin/statistics', [AdminController::class, 'statistics']);