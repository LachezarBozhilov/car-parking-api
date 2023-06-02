<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/parking/available-spots', [ParkingController::class, 'checkAvailableSpots']);
Route::get('/parking/amount-due/{vehicleNumber}', [ParkingController::class, 'checkAmountDue']);
Route::post('/parking/register', [ParkingController::class, 'registerVehicle']);
Route::post('/parking/deregister', [ParkingController::class, 'deregisterVehicle']);