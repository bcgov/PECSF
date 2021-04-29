<?php

use App\Http\Controllers\CharityController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/donate', [CharityController::class, 'select'])->name('donate');
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('donate')->name('donate.')->group(function () {
    Route::get('/select', [CharityController::class, 'index'])->name('select');
    Route::post('/remove', [CharityController::class, 'remove'])->name('select');
    Route::get('/amount', [CharityController::class, 'amount'])->name('amount');
    Route::get('/distribution', [CharityController::class, 'distribution'])->name('distribution');
    Route::get('/summary', [CharityController::class, 'distribution'])->name('summary');

    Route::post('/select', [CharityController::class, 'saveCharities'])->name('save.select');
    Route::post('/amount', [CharityController::class, 'saveAmount'])->name('save.amount');
    Route::post('/distribution', [CharityController::class, 'saveDistribution'])->name('save.distribution');
    Route::post('/summary', [CharityController::class, 'confirmDonation'])->name('save.summary');
});
