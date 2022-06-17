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
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\VolunteeringController;
use App\Http\Controllers\PledgeCharityController;
use App\Http\Controllers\Auth\AzureLoginController;
use App\Http\Controllers\Admin\BusinessUnitController;

use App\Http\Controllers\Admin\CampaignYearController;

use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\CampaignPledgeController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\CharityListMaintenanceController;
use App\Http\Controllers\Admin\FundSupportedPoolController;
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


Route::get('/donate', [CharityController::class, 'start'])->name('donate');
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('donations', [DonationController::class, 'index'])->middleware(['auth'])->name('donations.list');
Route::get('donations/old-pledge-detail', [DonationController::class, 'oldPledgeDetail'])->name('donation.old-pledge-detail');
Route::prefix('donate')->middleware(['auth','campaign'])->name('donate.')->group(function () {
    Route::get('/start', [CharityController::class, 'start'])->name('start');
    Route::get('/select', [CharityController::class, 'select'])->name('select-charities');
    Route::post('/remove', [CharityController::class, 'remove'])->name('select');
    Route::get('/amount', [CharityController::class, 'amount'])->name('amount');
    Route::get('/distribution', [CharityController::class, 'distribution'])->name('distribution');
    Route::get('/summary', [CharityController::class, 'summary'])->name('summary');
    Route::get('/thank-you', [CharityController::class, 'thankYou'])->name('save.thank-you');
    Route::get('/charities/{charity_id}',[CharityController::class, 'show']);

    Route::get('/regional-pool', [CharityController::class, 'regionalPool'])->name('regional-pool');
    Route::get('/regional-pool-detail/{id}', [CharityController::class, 'regionalPoolDetail'])->name('regional-pool-detail');

    Route::post('/start', [CharityController::class, 'savePoolOption'])->name('save.pool-option');
    Route::post('/select', [CharityController::class, 'saveCharities'])->name('save.select');
    Route::post('/amount', [CharityController::class, 'saveAmount'])->name('save.amount');
    Route::post('/distribution', [CharityController::class, 'saveDistribution'])->name('save.distribution');
    Route::post('/summary', [CharityController::class, 'confirmDonation'])->name('save.summary');

    Route::post('/regional-pool', [CharityController::class, 'saveRegionalPool'])->name('save.regional-pool');


    Route::get('/download-summary', [CharityController::class, 'savePDF'])->name('save.pdf');

    Route::get('/download/{file}', [PledgeController::class, 'download'])->name('download-charity');
});


Route::prefix('volunteering')->middleware(['auth'])->name('volunteering.')->group(function () {
    Route::get('/', [VolunteeringController::class, 'index'])->name('index');
    Route::post('/', [VolunteeringController::class, 'store'])->name('store');
});

Route::prefix('challenge')->middleware(['auth'])->name('challege.')->group(function () {
    Route::get('/', [ChallengeController::class, 'index'])->name('index');
    Route::post('/download', [ChallengeController::class, 'download'])->name('download');

});

Route::get('/contact', [ContactFaqController::class, 'index'])->middleware(['auth'])->name('contact');

Route::get('report', [PledgeCharityController::class, 'index'])->name('report');

// Route::group(['middleware' => ['auth']], function() {
//     Route::resource('campaignyears', CampaignYearController::class)->except(['destroy']);
// });

Route::middleware(['auth'])->prefix('administrators')->name('admin.')->group(function() {
    Route::get('dashboard', [AdministratorController::class, 'dashboard'])->name('dashboard');
    // Route::resource('/', AdministratorController::class)->only(['index','store']);
    // Route::get('/{administrator}/delete', [AdministratorController::class,'destroy']);
    // Route::get('/users', [AdministratorController::class,'getUsers']);
});


Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function() {

    // Campaign years
    Route::resource('campaignyears', CampaignYearController::class)->except(['destroy']);

    // Organizations
    Route::resource('/organizations', OrganizationController::class)->except(['create']);

    // Regions
    Route::resource('/regions', RegionController::class)->except(['create']);

    // Business Units
    Route::resource('/business-units', BusinessUnitController::class)->except(['create']);


    // Fund Supported Pools
    Route::get('/fund-supported-pools/charities', [FundSupportedPoolController::class,'getCharities']);
    Route::post('/fund-supported-pools/duplicate/{id}', [FundSupportedPoolController::class,'duplicate'])->name('fund-suppported-pools.duplicate');
    Route::resource('/fund-supported-pools', FundSupportedPoolController::class);


    // Administrators
    Route::resource('/administrators', AdministratorController::class)->only(['index','store', 'destroy']);
    Route::resource('/charity-list-maintenance', CharityListMaintenanceController::class)->only(['index','store', 'destroy']);
    Route::get('/administrators/users', [AdministratorController::class,'getUsers'])->name('administrators.users');
    // Route::get('/administrators/{administrator}/delete', [AdministratorController::class,'destroy']);

});

Route::middleware(['auth'])->prefix('admin-pledge')->name('admin-pledge.')->group(function() {

    // Pledge Administration - Campaign Pledge
    Route::resource('/campaign', CampaignPledgeController::class)->except(['destroy']);
    Route::get('/campaign-users', [CampaignPledgeController::class,'getUsers'])->name('administrators.users');

});

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function() {
    Route::get('/others', function() {
        return "to be developed";
    })->name('others');

    Route::get('/reporting', function() {
        return "to be developed";
    })->name('reporting');

});
