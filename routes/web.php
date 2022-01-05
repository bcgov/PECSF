<?php

use App\Http\Controllers\CharityController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AzureLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\ContactFaqController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\PledgeCharityController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\VolunteeringController;
use App\Http\Controllers\Admin\CampaignYearController;

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
Route::get('/login/microsoft', [AzureLoginController::class, 'login'])->name('ms-login');
Route::POST('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/login/microsoft/callback', [AzureLoginController::class, 'handleCallback'])->name('callback');

Route::get('/donate', [CharityController::class, 'select'])->name('donate');
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('donations', [DonationController::class, 'index'])->middleware(['auth'])->name('donations.list');
Route::prefix('donate')->middleware(['auth'])->name('donate.')->group(function () {
    Route::get('/select', [CharityController::class, 'index'])->name('select');
    Route::post('/remove', [CharityController::class, 'remove'])->name('select');
    Route::get('/amount', [CharityController::class, 'amount'])->name('amount');
    Route::get('/distribution', [CharityController::class, 'distribution'])->name('distribution');
    Route::get('/summary', [CharityController::class, 'distribution'])->name('summary');
    Route::get('/thank-you', [CharityController::class, 'saveDistribution'])->name('save.thank-you');

    Route::post('/select', [CharityController::class, 'saveCharities'])->name('save.select');
    Route::post('/amount', [CharityController::class, 'saveAmount'])->name('save.amount');
    Route::post('/summary', [CharityController::class, 'confirmDonation'])->name('save.summary');

    Route::get('/download-summary', [CharityController::class, 'savePDF'])->name('save.pdf');


    Route::get('/download/{file}', [PledgeController::class, 'download'])->name('download-charity');
});

Route::prefix('volunteering')->middleware(['auth'])->name('volunteering.')->group(function () {
    Route::get('/', [VolunteeringController::class, 'index'])->name('index');
});

Route::prefix('challenge')->middleware(['auth'])->name('challege.')->group(function () {
    Route::get('/', [ChallengeController::class, 'index'])->name('index');
});

Route::get('/contact', [ContactFaqController::class, 'index'])->middleware(['auth'])->name('contact');

Route::get('report', [PledgeCharityController::class, 'index'])->name('report');

Route::resource('campaignyears', CampaignYearController::class)->except(['destroy']);