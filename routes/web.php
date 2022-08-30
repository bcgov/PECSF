<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PledgeController;

use App\Http\Controllers\CharityController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DonateNowController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactFaqController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\VolunteeringController;

use App\Http\Controllers\PledgeCharityController;
use App\Http\Controllers\Auth\AzureLoginController;
use App\Http\Controllers\BankDepositFormController;


use App\Http\Controllers\Admin\CRACharityController;
use App\Http\Controllers\System\AccessLogController;
use App\Http\Controllers\Admin\BusinessUnitController;
use App\Http\Controllers\Admin\CampaignYearController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Auth\KeycloakLoginController;
use App\Http\Controllers\Admin\AdministratorController;

use App\Http\Controllers\Admin\CampaignPledgeController;
use App\Http\Controllers\Admin\DonationUploadController;
use App\Http\Controllers\System\UserMaintenanceController;
use App\Http\Controllers\Admin\FundSupportedPoolController;
use App\Http\Controllers\System\ScheduleJobAuditController;

use App\Http\Controllers\Auth\MicrosoftGraphLoginController;
use App\Http\Controllers\Admin\MaintainEventPledgeController;
use App\Http\Controllers\Admin\EventSubmissionQueueController;
use App\Http\Controllers\Admin\CharityListMaintenanceController;

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

Route::get('login/{provider}', [KeycloakLoginController::class, 'redirectToProvider'])
                 ->middleware('guest')->name('keycloak-login');
Route::get('login/{provider}/callback', [KeycloakLoginController::class, 'handleProviderCallback']);
Route::post('/logout', [KeycloakLoginController::class, 'destroy'])
                ->middleware('auth')->name('logout');
//Route::get('/login/microsoft', [AzureLoginController::class, 'login'])->name('ms-login');
//Route::POST('logout', [LoginController::class, 'logout'])->name('logout');
//Route::get('/login/microsoft/callback', [AzureLoginController::class, 'handleCallback'])->name('callback');
// MS Graph API Authenication -- composer require league/oauth2-client  microsoft/microsoft-graph
// Route::get('/login/microsoft', [MicrosoftGraphLoginController::class, 'signin'])
//                  ->middleware('guest')
//                  ->name('ms-login');
// Route::post('/logout', [MicrosoftGraphLoginController::class, 'destroy'])
//                 ->middleware('auth')
//                 ->name('logout');
//                 ->name('logout');
// Route::get('/login/microsoft/callback', [MicrosoftGraphLoginController::class, 'callback']);


Route::get('/donate', [CharityController::class, 'start'])->name('donate');
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('donations', [DonationController::class, 'index'])->middleware(['auth'])->name('donations.list');
Route::get('donations/pledge-detail', [DonationController::class, 'pledgeDetail'])->name('donations.pledge-detail');
Route::prefix('donate')->middleware(['auth','campaign'])->name('donate.')->group(function () {
    Route::get('/start', [CharityController::class, 'start'])->name('start');
    Route::get('/select', [CharityController::class, 'select'])->name('select-charities');
    Route::post('/remove', [CharityController::class, 'remove'])->name('select');
    Route::get('/amount', [CharityController::class, 'amount'])->name('amount');
    Route::get('/distribution', [CharityController::class, 'distribution'])->name('distribution');
    Route::get('/summary', [CharityController::class, 'summary'])->name('summary');
    Route::get('/thank-you', [CharityController::class, 'thankYou'])->name('save.thank-you');
    Route::get('/charities/{charity_id}',[CharityController::class, 'show']);
    Route::get('/duplicate/{pledge_id}',[CharityController::class, 'duplicate'])->name("duplicate");

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

// Donate Now
Route::middleware(['auth'])->group(function () {
    Route::resource('donate-now', DonateNowController::class)->except(['show','edit','update','destroy']);
    Route::get('/donate-now/thank-you', [DonateNowController::class, 'thankYou'])->name('donate-now.thank-you');
    Route::get('/donate-now/charities', [DonateNowController::class, 'searchCharities'])->name('donate-now.charities');
    Route::get('/donate-now/{id}/summary', [DonateNowController::class, 'summary'])->name('donate-now.summary');
    Route::get('/donate-now/regional-pool-detail/{id}', [DonateNowController::class, 'regionalPoolDetail'])->name('donate-now.regional-pool-detail');
});

Route::prefix('volunteering')->middleware(['auth'])->name('volunteering.')->group(function () {
    Route::get('/', [VolunteeringController::class, 'index'])->name('index');
    Route::post('/', [VolunteeringController::class, 'store'])->name('store');
    Route::get('/supply_order_form', [VolunteeringController::class, 'supply_order_form'])->name('supply_deposit_form');
});
Route::get('/profile', [VolunteeringController::class, 'profile'])->name('profile');
Route::get('/training', [VolunteeringController::class, 'training'])->name('trainging');

Route::get('/bank_deposit_form', [BankDepositFormController::class, 'index'])->name('bank_deposit_form');
Route::get('/bank_deposit_form/organization_code', [BankDepositFormController::class, 'organization_code'])->name('organization_code_ajax');
Route::get('/bank_deposit_form/organization_name', [BankDepositFormController::class, 'organization_name'])->name('organization_name_ajax');
Route::get('/bank_deposit_form/organizations', [BankDepositFormController::class, 'organizations'])->name('organizations');

Route::post('/bank_deposit_form', [BankDepositFormController::class, 'store'])->name('bank_deposit_form');


Route::prefix('challenge')->middleware(['auth'])->name('challege.')->group(function () {
    Route::get('/', [ChallengeController::class, 'index'])->name('index');
    Route::get('/daily_campaign', [ChallengeController::class, 'daily_campaign'])->name('daily_campaign');

    Route::get('/download', [ChallengeController::class, 'download'])->name('download');
    Route::get('/preview', [ChallengeController::class, 'preview'])->name('preview');
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


    // Business Units
    Route::resource('/charities', CRACharityController::class)->except(['create','destroy']);

    // Fund Supported Pools
    Route::get('/fund-supported-pools/charities', [FundSupportedPoolController::class,'getCharities']);
    Route::post('/fund-supported-pools/duplicate/{id}', [FundSupportedPoolController::class,'duplicate'])->name('fund-suppported-pools.duplicate');
    Route::resource('/fund-supported-pools', FundSupportedPoolController::class);


    // Administrators
    Route::resource('/administrators', AdministratorController::class)->only(['index','store', 'destroy']);
    Route::resource('/charity-list-maintenance', CharityListMaintenanceController::class)->only(['index','store', 'destroy']);
    Route::get('/administrators/users', [AdministratorController::class,'getUsers'])->name('administrators.users');
    // Route::get('/administrators/{administrator}/delete', [AdministratorController::class,'destroy']);


    // Access Log
    Route::get('/access-logs', [AccessLogController::class, 'index'])->name('access_logs');
    Route::get('/access-logs-user-detail/{id}', [AccessLogController::class, 'show']);

    // Schedule Job Audit
    Route::resource('/schedule-job-audits', ScheduleJobAuditController::class)->only(['index','show', 'destroy']);

});

Route::middleware(['auth'])->prefix('admin-pledge')->name('admin-pledge.')->group(function() {

    // Pledge Administration - Campaign Pledge
    Route::resource('/campaign', CampaignPledgeController::class);
    Route::get('/campaign-users', [CampaignPledgeController::class,'getUsers'])->name('administrators.users');
    Route::get('/campaign-nongov-user', [CampaignPledgeController::class,'getNonGovUserDetail'])->name('administrators.nongovuser');
    Route::get('/campaign-pledgeid', [CampaignPledgeController::class,'getCampaignPledgeID'])->name('administrators.pledgeid');

    Route::resource('/maintain-event', MaintainEventPledgeController::class)->except(['destroy']);
    Route::resource('/submission-queue', EventSubmissionQueueController::class)->except(['destroy']);
    Route::get('/create', [MaintainEventPledgeController::class,'createEvent'])->name('admin-pledge.create');

});

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function() {
    Route::get('/others', function() {
        return "to be developed";
    })->name('others');

});

Route::middleware(['auth'])->prefix('reporting')->name('reporting.')->group(function() {

    Route::resource('/donation-upload', DonationUploadController::class)->only(['index','store','show']);

});


Route::middleware(['auth'])->prefix('system')->name('system.')->group(function() {

    // Schedule Job Audit
    Route::resource('/schedule-job-audits', ScheduleJobAuditController::class)->only(['index','show', 'destroy']);

    // Users Maintenance
    Route::resource('/users', UserMaintenanceController::class)->only(['index','show', 'edit', 'update']);

    // Access Log
    Route::get('/access-logs', [AccessLogController::class, 'index'])->name('access-logs');
    Route::get('/access-logs-user', [AccessLogController::class, 'getUsers'])->name('access-logs.users');
    Route::get('/access-logs-user-detail/{id}', [AccessLogController::class, 'show']);

});
