<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactFaqController;
use App\Http\Controllers\VolunteeringController;
use App\Http\Controllers\PledgeCharityController;
use App\Http\Controllers\Auth\AzureLoginController;
use App\Http\Controllers\Admin\CampaignYearController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Auth\MicrosoftGraphLoginController;

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
//Route::get('/login/microsoft', [AzureLoginController::class, 'login'])->name('ms-login');
//Route::POST('logout', [LoginController::class, 'logout'])->name('logout');
//Route::get('/login/microsoft/callback', [AzureLoginController::class, 'handleCallback'])->name('callback');
// MS Graph API Authenication -- composer require league/oauth2-client  microsoft/microsoft-graph
Route::get('/login/microsoft', [MicrosoftGraphLoginController::class, 'signin'])
                 ->middleware('guest')
                 ->name('ms-login');
Route::post('/logout', [MicrosoftGraphLoginController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');
Route::get('/login/microsoft/callback', [MicrosoftGraphLoginController::class, 'callback']);


Route::get('/donate', [CharityController::class, 'select'])->name('donate');
Route::get('/donate/edit/{id?}', [CharityController::class, 'edit'])->name('donate.edit');
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('donations', [DonationController::class, 'index'])->middleware(['auth'])->name('donations.list');
Route::prefix('donate')->middleware(['auth'])->name('donate.')->group(function () {
    Route::get('/select', [CharityController::class, 'index'])->name('select');
    Route::post('/remove', [CharityController::class, 'remove'])->name('select');
    Route::get('/amount', [CharityController::class, 'amount'])->name('amount');
    Route::get('/distribution', [CharityController::class, 'distribution'])->name('distribution');
    Route::get('/summary', [CharityController::class, 'summary'])->name('summary');
    Route::get('/thank-you', [CharityController::class, 'thankYou'])->name('save.thank-you');
    Route::get('/charities/{charity_id}',[CharityController::class, 'show']); 

    Route::post('/select', [CharityController::class, 'saveCharities'])->name('save.select');
    Route::post('/amount', [CharityController::class, 'saveAmount'])->name('save.amount');
    Route::post('/distribution', [CharityController::class, 'saveDistribution'])->name('save.distribution');
    Route::post('/summary', [CharityController::class, 'confirmDonation'])->name('save.summary');

    Route::get('/download-summary', [CharityController::class, 'savePDF'])->name('save.pdf');


    Route::get('/download/{file}', [PledgeController::class, 'download'])->name('download-charity');
});


Route::prefix('volunteering')->middleware(['auth'])->name('volunteering.')->group(function () {
    Route::get('/', [VolunteeringController::class, 'index'])->name('index');
    Route::post('/', [VolunteeringController::class, 'store'])->name('store');
});

Route::prefix('challenge')->middleware(['auth'])->name('challege.')->group(function () {
    Route::get('/', [ChallengeController::class, 'index'])->name('index');
});

Route::get('/contact', [ContactFaqController::class, 'index'])->middleware(['auth'])->name('contact');

Route::get('report', [PledgeCharityController::class, 'index'])->name('report');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('campaignyears', CampaignYearController::class)->except(['destroy']);
});    

Route::group(['middleware' => ['auth']], function() {
    Route::resource('/administrators', AdministratorController::class)->only(['index','store']);
    Route::get('/administrators/{administrator}/delete', [AdministratorController::class,'destroy']);
    Route::get('/administrators/users', [AdministratorController::class,'getUsers']);
}); 