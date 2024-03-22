<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SystemStatusController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['api_token'])->group(function () {

    Route::get('/system/queue-status/{api_token}', [SystemStatusController::class, 'queueStatus']);
    Route::get('/system/schedule-status/{api_token}', [SystemStatusController::class, 'scheduleStatus']);

});
