<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HolidayListController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestedController;

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

Route::get('/employees', [EmployeeController::class, 'index']);

Route::get('/holidays', [HolidayListController::class, 'index'])->name('api.holidays.index');

Route::get('/leave-requested/current-year', [LeaveRequestedController::class, 'currentYear']);

Route::post('/leave-requested/search', [LeaveRequestedController::class, 'search']); 

Route::get('/leave-requested/userLeaves', [LeaveRequestedController::class, 'userLeaves']);
