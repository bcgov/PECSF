<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\DonationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DonateNowController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactFaqController;
use App\Http\Controllers\Admin\RegionController;

use App\Http\Controllers\VolunteeringController;
use App\Http\Controllers\AnnualCampaignController;

use App\Http\Controllers\BankDepositFormController;
use App\Http\Controllers\SpecialCampaignController;

use App\Http\Controllers\System\AuditingController;
use App\Http\Controllers\Admin\CRACharityController;
use App\Http\Controllers\System\AccessLogController;

use App\Http\Controllers\System\LogViewerController;
use App\Http\Controllers\VolunteerProfileController;
use App\Http\Controllers\Admin\PayCalendarController;
use App\Http\Controllers\System\UploadFileController;
use App\Http\Controllers\Admin\BusinessUnitController;
use App\Http\Controllers\Admin\CampaignYearController;
use App\Http\Controllers\Admin\DonationDataController;

use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\PledgeReportController;
use App\Http\Controllers\Admin\SupplyReportController;
use App\Http\Controllers\Auth\KeycloakLoginController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\System\AnnouncementController;
use App\Http\Controllers\System\UserActivityController;
use App\Http\Controllers\Admin\CampaignPledgeController;
use App\Http\Controllers\Admin\DonationUploadController;
use App\Http\Controllers\System\SystemSettingController;
use App\Http\Controllers\Admin\DonateNowPledgeController;
use App\Http\Controllers\System\ExportAuditLogController;
use App\Http\Controllers\Admin\CRACharityReportController;
use App\Http\Controllers\System\UserMaintenanceController;
use App\Http\Controllers\Admin\ChallengeSettingsController;
use App\Http\Controllers\Admin\FundSupportedPoolController;
use App\Http\Controllers\System\ScheduleJobAuditController;
use App\Http\Controllers\Admin\DonationDataReportController;
use App\Http\Controllers\Admin\MaintainEventPledgeController;
use App\Http\Controllers\Admin\PledgeCharityReportController;
use App\Http\Controllers\Admin\EventSubmissionQueueController;
use App\Http\Controllers\Admin\SpecialCampaignSetupController;
use App\Http\Controllers\Admin\CityRegionInformationController;
use App\Http\Controllers\Admin\SpecialCampaignPledgeController;
use App\Http\Controllers\Admin\CharityListMaintenanceController;
use App\Http\Controllers\Admin\EligibleEmployeeReportController;
use App\Http\Controllers\Admin\VolunteerProfileReportController;
use App\Http\Controllers\Admin\VolunteerProfileUploadController;
use App\Http\Controllers\Admin\ChallengePageDataReportController;
use App\Http\Controllers\Admin\MaintainVolunteerProfileController;
use App\Http\Controllers\Admin\GamingAndFundrasingReportController;
use App\Http\Controllers\Admin\ChallengeSummaryMaintenanceController;
use App\Http\Controllers\Admin\OrgPartipationTractorReportController;
use App\Http\Controllers\Admin\EligibleEmployeeSummaryMaintenanceController;
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

// For Administrator login                 
Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin-login');                
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


// Route::get('/donate', [CharityController::class, 'start'])->name('donate');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/visit-monitoring-log', [HomeController::class, 'updateVisitMonitoringLog'])->name('visit-monitoring-log');

    Route::get('donations', [DonationController::class, 'index'])->name('donations.list');
    Route::get('donations/pledge-detail', [DonationController::class, 'pledgeDetail'])->name('donations.pledge-detail');
});

// Route::prefix('donate')->middleware(['auth','campaign'])->name('donate.')->group(function () {
//     Route::get('/start', [CharityController::class, 'start'])->name('start');
//     Route::get('/select', [CharityController::class, 'select'])->name('select-charities');
//     Route::post('/remove', [CharityController::class, 'remove'])->name('select');
//     Route::get('/amount', [CharityController::class, 'amount'])->name('amount');
//     Route::get('/distribution', [CharityController::class, 'distribution'])->name('distribution');
//     Route::get('/summary', [CharityController::class, 'summary'])->name('summary');
//     Route::get('/thank-you', [CharityController::class, 'thankYou'])->name('save.thank-you');
//     Route::get('/charities/{charity_id}',[CharityController::class, 'show']);
//     Route::get('/duplicate/{pledge_id}',[CharityController::class, 'duplicate'])->name("duplicate");

//     Route::get('/regional-pool', [CharityController::class, 'regionalPool'])->name('regional-pool');
//     Route::get('/regional-pool-detail/{id}', [CharityController::class, 'regionalPoolDetail'])->name('regional-pool-detail');

//     Route::post('/start', [CharityController::class, 'savePoolOption'])->name('save.pool-option');
//     Route::post('/select', [CharityController::class, 'saveCharities'])->name('save.select');
//     Route::post('/amount', [CharityController::class, 'saveAmount'])->name('save.amount');
//     Route::post('/distribution', [CharityController::class, 'saveDistribution'])->name('save.distribution');
//     Route::post('/summary', [CharityController::class, 'confirmDonation'])->name('save.summary');

//     Route::post('/regional-pool', [CharityController::class, 'saveRegionalPool'])->name('save.regional-pool');


//     Route::get('/download-summary', [CharityController::class, 'savePDF'])->name('save.pdf');

//     Route::get('/download/{file}', [PledgeController::class, 'download'])->name('download-charity');
// });

// Annual Campaign (usually Sep - Nov)
Route::middleware(['auth'])->group(function () {

    Route::get('/annual-campaign/thank-you', [AnnualCampaignController::class, 'thankYou'])->name('annual-campaign.thank-you');
    Route::get('/annual-campaign/{id}/summary', [AnnualCampaignController::class, 'summaryPdf'])->name('annual-campaign.summary-pdf');
    Route::get('/annual-campaign/regional-pool-detail/{id}', [AnnualCampaignController::class, 'regionalPoolDetail'])->name('annual-campaign.regional-pool-detail');
    Route::get('/annual-campaign/valid-duplicate/{pledge_id}',[AnnualCampaignController::class, 'validDuplicate'])->name("annual-campaign.valid-duplicate");
    Route::post('/annual-campaign/duplicate/{pledge_id}',[AnnualCampaignController::class, 'duplicate'])->name("annual-campaign.duplicate");
    Route::resource('/annual-campaign', AnnualCampaignController::class)->only(['index', 'create', 'store']);
});

// Donate Now
Route::middleware(['auth'])->group(function () {
    Route::resource('donate-now', DonateNowController::class)->except(['show','edit','update','destroy']);
    Route::get('/donate-now/thank-you', [DonateNowController::class, 'thankYou'])->name('donate-now.thank-you');
    Route::get('/donate-now/charities', [DonateNowController::class, 'searchCharities'])->name('donate-now.charities');
    Route::get('/donate-now/{id}/summary', [DonateNowController::class, 'summary'])->name('donate-now.summary');
    Route::get('/donate-now/regional-pool-detail/{id}', [DonateNowController::class, 'regionalPoolDetail'])->name('donate-now.regional-pool-detail');
});

// Special Campaign (Donation)

Route::middleware(['auth'])->group(function () {
    Route::post('/special-campaign-banner-dismiss', [SpecialCampaignController::class, 'dismissSpecialCampaignBanner'])->name('special-campaign-banner.dismiss');

    Route::resource('special-campaign', SpecialCampaignController::class)->except(['show','edit','update','destroy']);
    Route::get('/special-campaign/thank-you', [SpecialCampaignController::class, 'thankYou'])->name('special-campaign.thank-you');
    Route::get('/special-campaign/{id}/summary', [SpecialCampaignController::class, 'summary'])->name('special-campaign.summary');

});

Route::prefix('volunteering')->middleware(['auth'])->name('volunteering.')->group(function () {

    // Landing Page
    Route::get('/', [VolunteeringController::class, 'index'])->name('index');
    
    // Profile
    Route::get('/profile/thank-you', [VolunteerProfileController::class, 'thankYou'])->name('profile.thank-you');
    Route::resource('profile', VolunteerProfileController::class)->except(['destroy']);

    // communication
    Route::view('/comminucation', '/volunteering/comminucation',[])->name('communication');

    // Supply Order Form 
    Route::get('/supply_order_form', [VolunteeringController::class, 'supply_order_form'])->name('supply_order_form');
    Route::post('/supply_order_form', [VolunteeringController::class, 'supply_order_form'])->name('supply_order_form');

});

Route::middleware(['auth'])->group(function () {

    Route::get('/bank_deposit_form', [BankDepositFormController::class, 'index'])->name('bank_deposit_form');
    Route::get('/bank_deposit_form/organization_code', [BankDepositFormController::class, 'organization_code'])->name('organization_code_ajax');
    Route::get('/bank_deposit_form/organization_name', [BankDepositFormController::class, 'organization_name'])->name('organization_name_ajax');
    Route::get('/bank_deposit_form/organizations', [BankDepositFormController::class, 'organizations'])->name('organizations');
    Route::get('/bank_deposit_form/bc_gov_id',[BankDepositFormController::class, 'bc_gov_id'])->name('bc_gov_id');
    Route::get('/bank_deposit_form/business_unit',[BankDepositFormController::class, 'business_unit'])->name('business_unit');
    Route::get('/bank_deposit_form/download/{id}',[BankDepositFormController::class, 'download'])->name('download');
    Route::get('/bank_deposit_form/{form_id}/delete/{fileName}',[BankDepositFormController::class, 'delete'])->name('delete');
    Route::post('/bank_deposit_form', [BankDepositFormController::class, 'store'])->name('bank_deposit_form');
    Route::post('/bank_deposit_form/update', [BankDepositFormController::class, 'update'])->name('bank_deposit_form.update');
    Route::post('/bank_deposit_form/media', [BankDepositFormController::class, 'storeMedia'])->name('bank_deposit_form.storeMedia');
    
    Route::post('/volunteering/supply_order_form', [VolunteeringController::class,'supply_order_form'])->name('supply_order_form');

});

Route::prefix('challenge')->middleware(['auth'])->name('challenge.')->group(function () {
    Route::get('/', [ChallengeController::class, 'index'])->name('index');
    Route::post('/', [ChallengeController::class, 'index']);
    Route::get('/daily_campaign', [ChallengeController::class, 'daily_campaign'])->name('daily_campaign');
    Route::get('/download', [ChallengeController::class, 'download'])->name('download');
    Route::get('/org_participation_tracker', [ChallengeController::class, 'org_participation_tracker'])->name('org_participation_tracker');
    Route::get('/org_participation_tracker_download', [ChallengeController::class, 'org_participation_tracker_download'])->name('org_participation_tracker_download');  
});

Route::get('/contact', [ContactFaqController::class, 'index'])->middleware(['auth'])->name('contact');


Route::middleware(['auth'])->prefix('administrators')->name('admin.')->group(function() {
    
    Route::get('dashboard', [AdministratorController::class, 'dashboard'])->name('dashboard');

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

    // Cities
    Route::resource('/cities', CityRegionInformationController::class)->only(['index']);


    // Pay Calendars
    Route::resource('/pay-calendars', PayCalendarController::class)->only(['index']);

    // CRA Charity
    // Route::get('/charities/export', [CRACharityController::class,'export2csv'])->name('charities.export2csv');
    // Route::get('/charities/export-progress/{id}', [CRACharityController::class,'exportProgress'])->name('charities.export2csv-progress');
    // Route::get('/charities/download-export-file/{id}', [CRACharityController::class,'downloadExportFile'])->name('charities.download-export-file');
    Route::resource('/charities', CRACharityController::class)->except(['create','destroy']);
    Route::resource('/charity-list-maintenance', CharityListMaintenanceController::class)->only(['index','store', 'show']);

    // Special Campaign Setup
    Route::get('/special-campaigns/charities', [SpecialCampaignSetupController::class,'getCharities']);
    Route::resource('/special-campaigns', SpecialCampaignSetupController::class)->except('create');

    // Fund Supported Pools
    Route::get('/fund-supported-pools/charities', [FundSupportedPoolController::class,'getCharities']);
    Route::post('/fund-supported-pools/duplicate/{id}', [FundSupportedPoolController::class,'duplicate'])->name('fund-suppported-pools.duplicate');
    Route::resource('/fund-supported-pools', FundSupportedPoolController::class);

    // Challenge Settings
    Route::get('/', [SettingsController::class,'index'])->name('others');
    Route::get('/challenge', [ChallengeSettingsController::class,'index'])->name('challenge');
    Route::post('/challenge', [ChallengeSettingsController::class,'store'])->name('challenge.update');
    Route::post('/challenge/finalize_challenge_data', [ChallengeSettingsController::class,'finalizeChallengeData'])->name('challenge.finalize_challenge_data');

    // Challenge Summary Maintenance
    Route::resource('/challenge-summary', ChallengeSummaryMaintenanceController::class)->except('create');

    // Eligible Employees Summary 
    Route::resource('/eligible-employee-summary', EligibleEmployeeSummaryMaintenanceController::class)->except('create');


    Route::get('/volunteering', [SettingsController::class,'volunteering'])->name('volunteering');
    Route::post('/change', [SettingsController::class,'changeSetting'])->name('change');

});

Route::middleware(['auth'])->prefix('admin-pledge')->name('admin-pledge.')->group(function() {
    // Pledge Administration - Campaign Pledge
    Route::resource('/campaign', CampaignPledgeController::class);
    Route::post('/campaign/{id}/cancel', [CampaignPledgeController::class,'cancel'])->name('campaign-pledge.cancel');
    Route::get('/campaign-users', [CampaignPledgeController::class,'getUsers'])->name('administrators.users');
    Route::get('/campaign-nongov-user', [CampaignPledgeController::class,'getNonGovUserDetail'])->name('administrators.nongovuser');
    Route::get('/campaign-pledgeid', [CampaignPledgeController::class,'getCampaignPledgeID'])->name('administrators.pledgeid');

    // Event Maintainance Listing
    Route::resource('/maintain-event', MaintainEventPledgeController::class)->only(['index','show', 'create','edit','update']);
    // Route::get('/create', [MaintainEventPledgeController::class,'createEvent'])->name('admin-pledge.create');

    // Event Submission Queue
    Route::resource('/submission-queue', EventSubmissionQueueController::class)->only(['status','details','index']);
    Route::get('/details', [EventSubmissionQueueController::class,"details"])->name('details');
    Route::post('/status', [EventSubmissionQueueController::class,"status"])->name('status');
});


Route::middleware(['auth'])->prefix('admin-pledge')->name('admin-pledge.')->group(function() {
    // Pledge Administration -- Donate Now Pledges
    Route::resource('/donate-now', DonateNowPledgeController::class);
    Route::post('/donate-now/{id}/cancel', [DonateNowPledgeController::class,'cancel'])->name('donate-now.cancel');
    Route::get('/donate-now-users', [DonateNowPledgeController::class,'getUsers'])->name('donate-now.users');
});

// Special Campaign
Route::middleware(['auth'])->prefix('admin-pledge')->name('admin-pledge.')->group(function() {
    // Pledge Administration -- Special  Pledges
    Route::post('/special-campaign/{id}/cancel', [SpecialCampaignPledgeController::class,'cancel'])->name('special-campaign.cancel');
    Route::resource('/special-campaign', SpecialCampaignPledgeController::class);

});

// Administration -- Volunteering 
Route::middleware(['auth'])->prefix('admin-volunteering')->name('admin-volunteering.')->group(function() {
    // Pledge Administration -- Volunteer Profile 
    Route::get('/profile-users', [MaintainVolunteerProfileController::class,'getUsers'])->name('profile.users');
    Route::resource('/profile', MaintainVolunteerProfileController::class);

    // Profile - Upload
    Route::resource('/upload-profile', VolunteerProfileUploadController::class)->only(['index','store','show']);
});



Route::middleware(['auth'])->prefix('reporting')->name('reporting.')->group(function() {

    Route::resource('/donation-upload', DonationUploadController::class)->only(['index','store','show']);
    Route::resource('/donation-data', DonationDataController::class)->only(['index']);

    Route::get('/donation-report/export', [DonationDataReportController::class,'export2csv'])->name('donation-report.export2csv');
    Route::get('/donation-report/export-progress/{id}', [DonationDataReportController::class,'exportProgress'])->name('donation-report.export2csv-progress');
    Route::get('/donation-report/download-export-file/{id}', [DonationDataReportController::class,'downloadExportFile'])->name('donation-report.download-export-file');
    Route::resource('/donation-report', DonationDataReportController::class)->only(['index', 'show']);

    // Eligible Employee Count
    // Route::resource('/eligible-employee-count', EligibleEmployeeCountController::class)->only(['index']);
    // Eligible Employee Reporting
    Route::get('/eligible-employees/export', [EligibleEmployeeReportController::class,'export2csv'])->name('eligible-employees.export2csv');
    Route::get('/eligible-employees/export-progress/{id}', [EligibleEmployeeReportController::class,'exportProgress'])->name('eligible-employees.export2csv-progress');
    Route::get('/eligible-employees/download-export-file/{id}', [EligibleEmployeeReportController::class,'downloadExportFile'])->name('eligible-employees.download-export-file');
    Route::resource('/eligible-employees', EligibleEmployeeReportController::class)->only(['index']);

    // Organization Partipation Tracker
    Route::get('/org-partipation-tracker/export', [OrgPartipationTractorReportController::class,'export2csv'])->name('org-partipation-tracker.export2csv');
    Route::get('/org-partipation-tracker/export-progress', [OrgPartipationTractorReportController::class,'exportProgress'])->name('org-partipation-tracker.export2csv-progress');
    Route::get('/org-partipation-tracker/download-export-file/{id}', [OrgPartipationTractorReportController::class,'downloadExportFile'])->name('org-partipation-tracker.download-export-file');
    Route::get('/org-partipation-tracker/filter-ids', [OrgPartipationTractorReportController::class,'filteredIds'])->name('org-partipation-tracker.filtered-ids');
    Route::get('/org-partipation-tracker/download-export-files-in-zip', [OrgPartipationTractorReportController::class,'downloadExportFilesInZip'])->name('org-partipation-tracker.download-export-files-in-zip');
    Route::resource('/org-partipation-tracker', OrgPartipationTractorReportController::class)->only(['index','show']);

    // Challenge Page Data 
    Route::get('/challenge-page-data', [ChallengePageDataReportController::class,'index'])->name('challenge-page-data');
    Route::get('/challenge-page-data/date-options', [ChallengePageDataReportController::class,'getDateOptions'])->name('challenge-page-data.date-options');
    
    // Annual and Event Pledge Report
    Route::get('/pledges/export', [PledgeReportController::class,'export2csv'])->name('pledges.export2csv');
    Route::get('/pledges/export-progress/{id}', [PledgeReportController::class,'exportProgress'])->name('pledges.export2csv-progress');
    Route::get('/pledges/download-export-file/{id}', [PledgeReportController::class,'downloadExportFile'])->name('pledges.download-export-file');
    Route::resource('/pledges', PledgeReportController::class)->only(['index', 'show']);

    // Annual and Event Charities Report
    Route::get('/pledge-charities/export', [PledgeCharityReportController::class,'export2csv'])->name('pledge-charities.export2csv');
    Route::get('/pledge-charities/export-progress/{id}', [PledgeCharityReportController::class,'exportProgress'])->name('pledge-charities.export2csv-progress');
    Route::get('/pledge-charities/download-export-file/{id}', [PledgeCharityReportController::class,'downloadExportFile'])->name('pledge-charities.download-export-file');
    Route::resource('/pledge-charities', PledgeCharityReportController::class)->only(['index', 'show']);

    // Charities Report
    Route::get('/cra-charities/export', [CRACharityReportController::class,'export2csv'])->name('cra-charities.export2csv');
    Route::get('/cra-charities/export-progress/{id}', [CRACharityReportController::class,'exportProgress'])->name('cra-charities.export2csv-progress');
    Route::get('/cra-charities/download-export-file/{id}', [CRACharityReportController::class,'downloadExportFile'])->name('cra-charities.download-export-file');
    Route::resource('/cra-charities', CRACharityReportController::class)->only(['index', 'show']);

    // Gaming and Fundrasing Report
    Route::get('/gaming-and-fundrasing/export', [GamingAndFundrasingReportController::class,'export2csv'])->name('gaming-and-fundrasing.export2csv');
    Route::get('/gaming-and-fundrasing/export-progress/{id}', [GamingAndFundrasingReportController::class,'exportProgress'])->name('gaming-and-fundrasing.export2csv-progress');
    Route::get('/gaming-and-fundrasing/download-export-file/{id}', [GamingAndFundrasingReportController::class,'downloadExportFile'])->name('gaming-and-fundrasing.download-export-file');
    Route::resource('/gaming-and-fundrasing', GamingAndFundrasingReportController::class)->only(['index', 'show']);

    // Volunteer Profile Report
    Route::get('/volunteer-profiles/export', [VolunteerProfileReportController::class,'export2csv'])->name('volunteer-profiles.export2csv');
    Route::get('/volunteer-profiles/export-progress/{id}', [VolunteerProfileReportController::class,'exportProgress'])->name('volunteer-profiles.export2csv-progress');
    Route::get('/volunteer-profiles/download-export-file/{id}', [VolunteerProfileReportController::class,'downloadExportFile'])->name('volunteer-profiles.download-export-file');
    Route::resource('/volunteer-profiles', VolunteerProfileReportController::class)->only(['index', 'show']);

    //
    Route::resource('/supply-report', SupplyReportController::class)->only(['index','store']);
    Route::get('/supply-report/delete', [SupplyReportController::class,"delete"])->name('delete');
    Route::get('/supply-report/export', [SupplyReportController::class,"export"])->name('export');


});


Route::middleware(['auth'])->prefix('system')->name('system.')->group(function() {

    // System Security Control
    Route::resource('/settings', SystemSettingController::class)->only(['index','store']);

    // Schedule Job Audit
    Route::resource('/schedule-job-audits', ScheduleJobAuditController::class)->only(['index','show', 'destroy']);

    // Administrators
    Route::resource('/administrators', AdministratorController::class)->only(['index','store', 'destroy']);
    Route::get('/administrators/users', [AdministratorController::class,'getUsers'])->name('administrators.users');

    // Users Maintenance
    Route::post('/users/{id}/lock', [UserMaintenanceController::class,'lockUser'])->name('users.lock');
    Route::post('/users/{id}/unlock', [UserMaintenanceController::class,'unlockUser'])->name('users.unlock');
    Route::resource('/users', UserMaintenanceController::class)->only(['index','show', 'edit', 'update']);

    // Access Log
    Route::get('/access-logs', [AccessLogController::class, 'index'])->name('access-logs');
    Route::get('/access-logs-user', [AccessLogController::class, 'getUsers'])->name('access-logs.users');
    Route::get('/access-logs-user-detail/{id}', [AccessLogController::class, 'show']);

    // Auditing
    Route::resource('/auditing', AuditingController::class)->only(['index']);

    // Export Audit Log
    Route::resource('/export-audits', ExportAuditLogController::class)->only(['index']);

    // Announcement 
    Route::resource('/announcement', AnnouncementController::class)->only(['index','store']);
    Route::post('image-upload',[AnnouncementController::class, 'storeImage'])->name('announcement.image.upload');

    // User Activity & Behavior Analytics
    Route::get('/page-visits-overview', [UserActivityController::class, 'page_visits_overview'])->name('page-visits-overview');
    Route::get('/transaction-counts-overview', [UserActivityController::class, 'transaction_counts_overview'])->name('transaction-counts-overview');
    Route::get('/transaction-timings', [UserActivityController::class, 'transaction_timings'])->name('transaction-timings');

});

Route::middleware(['role:sysadmin'])->prefix('system')->name('system.')->group(function() {

        // Log files 
        Route::resource('/log-files', LogViewerController::class)->only(['index','show']);
        // Upload and download file (seed)
        Route::resource('/upload-files', UploadFileController::class)->only(['index','store','show']);
        // phpinfo page
        Route::get('/phpinfo-page',  [LogViewerController::class,'phpinfo_page'])->name('phpinfo_page');

});
