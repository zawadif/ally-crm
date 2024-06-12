<?php


use App\Enums\RelationEnum;
use App\Http\Controllers\Administrator\ApplicantController;
use App\Http\Controllers\Administrator\OfficeController;
use App\Http\Controllers\Administrator\RoleController;
use App\Http\Controllers\Administrator\SaleController;
use App\Http\Controllers\Administrator\UnitController;
use App\Mail\AdminInviteMail;
//use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('/login');
});
Route::group(['middleware' => 'auth'], function () {
Route::get('/fetchData', [App\Http\Controllers\HomeController::class, 'anyData'])->name('datatables.data');
Route::get('/getUsers', [App\Http\Controllers\HomeController::class, 'getUsers'])->name('listing');
Route::get('/interceptUrl', [App\Http\Controllers\HomeController::class, 'interceptUrl'])->name('interceptUrl');
    /*============================Admin auth routes goes here========================== */
    Route::get('configuration-variable-edit', [App\Http\Controllers\Admin\ConfigurationController::class, 'index'])->name('configuration-variable-edit');
    Route::get('configuration-variables', [App\Http\Controllers\Admin\ConfigurationController::class, 'view'])->name('configuration-variables');
    Route::post('configuration-variable-update', [App\Http\Controllers\Admin\ConfigurationController::class, 'update'])->name('configuration-variable-update');
});

Route::get('/login', function () {
    $type = request('type');
    return view('auth.login', ['type' => strtolower($type)]);
})->name('login')->middleware('guest');

// web Portal Sign up and Registration


// Sign up by invitation token
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard')->middleware(['auth', 'userStatus']);
Route::get('/chart/{id?}', [App\Http\Controllers\HomeController::class, 'chart'])->name('chart');
// read unread notification from header
Route::get('/supportTicket', [App\Http\Controllers\HomeController::class, 'readSupport'])->name('readSupport')->middleware(['auth']);
Route::get('/matchDispute', [App\Http\Controllers\HomeController::class, 'matchDispute'])->name('readMatchDispute')->middleware(['auth']);

//Route::get('seeding',[App\Http\Controllers\Administrator\SeedController::class,'storePermissions'] );
    Route::get('/clear-cache', function() {
        Artisan::call('route:cache');
        Artisan::call('route:clear');
	});
//season
Route::group( ['middleware' => 'auth' ], function(){
    Route::get('/getUsersDashboard', [App\Http\Controllers\HomeController::class, 'userDetailDashbaord'])->name('getUsersDashboard');
    Route::get('/fetch-data', [App\Http\Controllers\HomeController::class,'fetchData'])->name('fetch.data');
    Route::GET('/applicant-home-details-stats', [App\Http\Controllers\HomeController::class,'applicantHomeDetailStats'])->name('applicant_home_details_stats');
    Route::get('/fetch-cvs-data', [App\Http\Controllers\HomeController::class, 'fetchCVSData'])->name('fetch.cvs.data');
    Route::get('/client-cvs-data/{source}/{startDate}/{endDate}/{jobCategory}', [App\Http\Controllers\HomeController::class, 'clientCVSData'])->name('client.cvs.data');
    Route::post('/sale_open', [App\Http\Controllers\HomeController::class,'saleOpen'])->name('sale.open');
    Route::get('/get-sale_open/{startDate}/{endDate}', [App\Http\Controllers\HomeController::class,'getOpenSales']);
    Route::post('/sale_close', [App\Http\Controllers\HomeController::class,'saleClose'])->name('sale.close');
    Route::get('/get-sale_close/{startDate}/{endDate}', [App\Http\Controllers\HomeController::class,'getCloseSales']);

    //seed roles and permission
//    Route::get('seeding',[App\Http\Controllers\Administrator\SeedController::class,'storePermissions'] );
    Route::get('/users',[App\Http\Controllers\Administrator\TeamController::class,'listing'])->name('users');
    Route::post('/teams',[App\Http\Controllers\Administrator\TeamController::class,'store'])->name('teams');
    Route::get('/teams/all',[App\Http\Controllers\Administrator\TeamController::class,'teams']);
    Route::get('/userView/{id}',[App\Http\Controllers\Administrator\TeamController::class,'viewUser']);
    Route::get('/users/login_detail/{id}',[App\Http\Controllers\Administrator\TeamController::class,'loginDetail']);
    Route::get('/teamEdit/{id}',[App\Http\Controllers\Administrator\TeamController::class,'edit']);
    Route::post('/teamUpdate/{id}',[App\Http\Controllers\Administrator\TeamController::class,'update']);
    Route::post('users/resetPassword',[App\Http\Controllers\Administrator\TeamController::class,'resetPassword']);
    Route::post('/users/{id}/updateStatus', [App\Http\Controllers\Administrator\TeamController::class, 'updateUserStatus'])->name('users.updateStatus');
    Route::post('/users/{id}/delete', [App\Http\Controllers\Administrator\TeamController::class, 'userDelete'])->name('users.delete');
    Route::get('/users/activity/{id}', [App\Http\Controllers\Administrator\TeamController::class, 'userActivity'])->name('users.active');
   //roles route


//    Route::get('roles',[App\Http\Controllers\Administrator\RoleController::class,'index']);
//    Route::resource('roles', 'Administrator\RoleController');
    Route::resource('roles', RoleController::class);
    Route::get('/roles-all',[App\Http\Controllers\Administrator\RoleController::class,'getRoles'])->name('roles-all');
    Route::post('/assign-role-to-users',[App\Http\Controllers\Administrator\RoleController::class,'assignRoleToUsers'])->name('/assign-role-to-users');
//    Route::delete('/roles/{id}', 'RoleController@destroy')->name('roles.destroy');
//applicant or clients route
    Route::resource('clients', ApplicantController::class);

//    Route::get('/clients',[App\Http\Controllers\Administrator\ApplicantController::class,'index'])->name('clients');
    Route::get('/getApplicants',[App\Http\Controllers\Administrator\ApplicantController::class,'getApplicants'])->name('getApplicants');
    Route::get('/applicant_detail/{id}',[App\Http\Controllers\Administrator\ApplicantController::class,'applicantDetail'])->name('applicant_detail');
    Route::post('applicant_notes_block_casual',[App\Http\Controllers\Administrator\ApplicantController::class,'store_block_or_casual_notes'])->name('block_or_casual_notes');
    Route::get('applicant_notes/{id}',[App\Http\Controllers\Administrator\ApplicantController::class,'moduleNotesClients'])->name('applicant_notes');
    Route::get('cv_quality_notes/{id}',[App\Http\Controllers\Administrator\ApplicantController::class,'cvQualityNotesClients'])->name('cv_quality_notes');
    Route::get('download-updated-applicant-cv/{cv_id}',[App\Http\Controllers\Administrator\ApplicantController::class,'getUpdatedDownloadApplicantCv'])->name('downloadUpdatedApplicantCv');
    Route::get('download-applicant-cv/{cv_id}',[App\Http\Controllers\Administrator\ApplicantController::class,'getDownloadApplicantCv'])->name('downloadApplicantCv');
    Route::post('import-applicant-cv-file',[App\Http\Controllers\Administrator\ApplicantController::class,'UploadApplicantCV'])->name('import_applicantCv');
    Route::get('applicant-history/{applicant__history_id}',[App\Http\Controllers\Administrator\ApplicantController::class,'getApplicantHistory'])->name('applicantHistory');
//    Route::get('notes/history/{applicant__history_id}',[App\Http\Controllers\Administrator\ApplicantController::class,'getApplicantHistory'])->name('applicantHistory');
    Route::get('/notes/history/{id}', [App\Http\Controllers\Administrator\ApplicantController::class,'history'])->name('notes.history');
    Route::post('applicant-csv-file',[App\Http\Controllers\Administrator\ApplicantController::class,'getUploadApplicantCsv'])->name('applicantCsv');


//offices routes
    Route::resource('offices', OfficeController::class);
    Route::get('/getOffices',[App\Http\Controllers\Administrator\OfficeController::class,'getOffices'])->name('getOffices');
//module routes
//    Route::post('/module-notes-history', 'Administrator\ModuleNoteController@index')->name('notesHistory');
    Route::post('/module-notes-history',[App\Http\Controllers\Administrator\ModuleNoteController::class,'index'])->name('notesHistory');
//    Route::post('/module-note',[App\Http\Controllers\Administrator\ModuleNoteController::class,'store'])->name('module-note.store');
    Route::post('/module-note-store', [App\Http\Controllers\Administrator\ModuleNoteController::class, 'store'])->name('module-note-store');
    Route::get('office_notes/{id}',[App\Http\Controllers\Administrator\OfficeController::class,'moduleNotesClients'])->name('office_notes');
//Unites route
    Route::resource('units', UnitController::class);
    Route::get('/getUnits',[App\Http\Controllers\Administrator\UnitController::class,'getUnits'])->name('getUnits');
    Route::get('unit_notes/{id}',[App\Http\Controllers\Administrator\UnitController::class,'moduleNotesClients'])->name('unit_notes');

    Route::resource('sales', \App\Http\Controllers\Administrator\SaleController::class);
    Route::get('/getSales',[App\Http\Controllers\Administrator\SaleController::class,'getSales'])->name('getSales');
//    Route::get('/get-head-units/{headOfficeId}', [\App\Http\Controllers\Administrator\SaleController::class, 'getHeadUnit'])->name('getHeadUnit');
    Route::get('/get-head-units/{headOfficeId}', [\App\Http\Controllers\Administrator\SaleController::class, 'getHeadUnit']);
// Add these routes to your web.php file
//    Route::post('/sale/close/{saleId}', [SaleController::class, 'closeSale']);
    Route::post('/close-sale-with-notes', [SaleController::class, 'closeSale']);
    Route::post('/sale-on-hold-with-notes', [SaleController::class, 'onHoldSale']);
    Route::post('/sale-un-hold-with-notes', [SaleController::class, 'unHoldSale']);
    Route::get('all-closed-sales', [SaleController::class, 'getAllClosedSales'])->name('close_sales');
    Route::get('closed-sales', [SaleController::class, 'allClosedSales']);
    Route::get('sale_notes/{id}',[App\Http\Controllers\Administrator\SaleController::class,'saleNote'])->name('sale_notes');
    Route::GET('/user-statistics', [App\Http\Controllers\HomeController::class,'userStatistics'])->name('userStatistics');
    Route::get('sale-history/{sale__history_id}', [SaleController::class,'getSaleHistory'])->name('saleHistory');


    Route::resource('special_lists', \App\Http\Controllers\Administrator\SpecialistTitleController::class);
    Route::get('/getSpecialist',[App\Http\Controllers\Administrator\SpecialistTitleController::class,'getSpecialist'])->name('getSpecialist');
    Route::get('/get-special-titles/{category}',[App\Http\Controllers\Administrator\SpecialistTitleController::class,'getSpecialTitles'])->name('getSpecialistTitle');
//resource routes
    Route::get('job-nurse-resource',[App\Http\Controllers\Administrator\ResourceController::class,'getNurseSales'])->name('getDirectNurse');
    Route::get('getNursingJob',[App\Http\Controllers\Administrator\ResourceController::class,'getNursingJob']);
    Route::get('clients-within-15-km/{id}/{radius?}',[App\Http\Controllers\Administrator\ResourceController::class,'get15kmclients'])->name('range');
    Route::get('get15kmApplicantsAjax/{id}/{radius?}',[App\Http\Controllers\Administrator\ResourceController::class,'get15kmClientsAjax']);
    Route::get('applicants-blocked-in-last-2-months',[App\Http\Controllers\Administrator\ResourceController::class,'getLast2MonthsBlockedApplicantAdded'])->name('last2monthsBlockedApplicants');
    Route::get('getlast2MonthsBlockedAppAjax',[App\Http\Controllers\Administrator\ResourceController::class,'getLast2MonthsBlockedApplicantAddedAjax']);
    Route::post('unblock-notes',[App\Http\Controllers\Administrator\ResourceController::class,'storeUnblockNotes'])->name('unblock_notes');
    Route::get('temp-not-interested-clients',[App\Http\Controllers\Administrator\ResourceController::class,'getTempNotInterestedApplicants'])->name('TempNotInterestedApplicants');
    Route::get('getTempNotInterestedApplicantsAjax',[App\Http\Controllers\Administrator\ResourceController::class,'get_temp_not_interested_applicants_ajax']);
    Route::post('applicant_notes_unblock',[App\Http\Controllers\Administrator\ResourceController::class,'store_interested_notes'])->name('interested_notes');
    Route::get('sent-to-nurse-home',[App\Http\Controllers\Administrator\ApplicantController::class,'getNurseHomeApplicant'])->name('sentToNurseHome');
//    Route::get('sent-applicant-to-call-back-list','Administrator\ResourceController@getApplicantSentToCallBackList')->name('sentToCallBackList');
    Route::get('clients-added-in-last-7-days',[App\Http\Controllers\Administrator\ResourceController::class,'getLast7DaysApplicantAdded'])->name('last7days');
    Route::get('getlast7DaysApp',[App\Http\Controllers\Administrator\ResourceController::class,'get7DaysApplicants']);
    Route::get('clients-added-in-last-21-days',[App\Http\Controllers\Administrator\ResourceController::class,'getLast21DaysApplicantAdded'])->name('last21days');
    Route::get('getlast21DaysApp',[App\Http\Controllers\Administrator\ResourceController::class,'get21DaysApplicants']);
    Route::get('clients-added-in-last-2-months',[App\Http\Controllers\Administrator\ResourceController::class,'getLast2MonthsApplicantAdded'])->name('last2months');
    Route::get('get2MonthsApplicants',[App\Http\Controllers\Administrator\ResourceController::class,'get2MonthsApplicants']);
    Route::get('available-jobs/{id}',[App\Http\Controllers\Administrator\ResourceController::class,'get15kmAvailableJobs'])->name('jobs');
    Route::post('unblock_block_applicants',[App\Http\Controllers\Administrator\ApplicantController::class,'ajax_unblock_applicants'])->name('unblockBlockApplicants');
    Route::post('revertTempInterestAjax',[App\Http\Controllers\Administrator\ApplicantController::class,'revertTempInterest'])->name('revertTempInterestAjax');
    Route::get('potential-call-back-clients',[App\Http\Controllers\Administrator\ResourceController::class,'potentialCallBackApplicants'])->name('potential-call-back-clients');
    Route::get('get-call-back-clients',[App\Http\Controllers\Administrator\ResourceController::class,'getPotentialCallBackApplicants'])->name('get-call-back-clients');
    Route::get('revert-applicant-to-search-list',[App\Http\Controllers\Administrator\ResourceController::class,'getApplicantRevertToSearchList'])->name('revertCallBackApplicants');


    Route::get('no-response-clients',[App\Http\Controllers\Administrator\ResourceController::class,'getNotResponseApplicants'])->name('NoResponseApplicants');
    Route::get('getNoResponseApplicantsAjax',[App\Http\Controllers\Administrator\ResourceController::class,'get_no_response_applicants'])->name('getNoResponseApplicantsAjax');
    Route::post('active_response_notes',[App\Http\Controllers\Administrator\ResourceController::class,'store_active_respnse_notes'])->name('activeResponseNotest');
    Route::post('/applicant-rejected-history', [App\Http\Controllers\Administrator\ResourceController::class ,'applicantRejectedHistory'])->name('rejectedHistory');
//    Route::get('active-applicants-within-15-km/{id}',[App\Http\Controllers\Administrator\ResourceController::class ,'getActive15kmApplicants'])->name('15kmrange');
    Route::post('mark-applicant',[App\Http\Controllers\Administrator\ResourceController::class ,'getMarkApplicant'])->name('markApplicant');
    Route::get('sent-applicant-to-call-back-list',[App\Http\Controllers\Administrator\ResourceController::class ,'getApplicantSentToCallBackList'])->name('sentToCallBackList');
    Route::get('getNonNursingJob',[App\Http\Controllers\Administrator\ResourceController::class ,'getNonNursingJob']);
    Route::get('job-non-nurse-resource',[App\Http\Controllers\Administrator\ResourceController::class ,'getNonNurseSales'])->name('getDirectNonNurse');
    Route::get('job-non-nurse-specialist-resource',[App\Http\Controllers\Administrator\ResourceController::class ,'getNonNurseSpecialistSales'])->name('getDirectNonNurseSpecialist');
    Route::get('getNonNursingSpecialistJob',[App\Http\Controllers\Administrator\ResourceController::class ,'getNonNursingSpecialistJob']);
    Route::get('active-Carbon::now()->format("H:i:s");/{id}',[App\Http\Controllers\Administrator\ResourceController::class ,'getActive15kmApplicants'])->name('15kmrange');
    Route::get('get15kmJobsAvailableAjax/{id}',[App\Http\Controllers\Administrator\ResourceController::class ,'get15kmJobsAvailableAjax']);



    Route::get('quality-sales',[App\Http\Controllers\Administrator\QualityController::class,'qualitySales'])->name('quality-sales');
    Route::get('get-quality-sales',[App\Http\Controllers\Administrator\QualityController::class,'getQualitySales'])->name('get-quality-sales');
    Route::get('quality-sales-cleared',[App\Http\Controllers\Administrator\QualityController::class,'clearedSales'])->name('quality-sales-cleared');
    Route::get('get-cleared-sales',[App\Http\Controllers\Administrator\QualityController::class,'getClearedSales'])->name('get-cleared-sales');
    Route::get('quality-sales-rejected',[App\Http\Controllers\Administrator\QualityController::class,'rejectedSales'])->name('quality-sales-rejected');
    Route::get('get-rejected-sales',[App\Http\Controllers\Administrator\QualityController::class,'getRejectedSales'])->name('get-rejected-sales');
    Route::get('all-applicants-sent-cv-list',[App\Http\Controllers\Administrator\QualityController::class,'getAllApplicantWithSentCv'])->name('applicantWithSentCv');
    Route::get('get-quality-cv-applicants',[App\Http\Controllers\Administrator\QualityController::class,'getQualityCVclients']);
    Route::post('clear-reject-sale',[App\Http\Controllers\Administrator\QualityController::class,'clearRejectSale'])->name('clear-reject-sale');
    Route::get('update-confirm-interview/{id}/{viewString}',[App\Http\Controllers\Administrator\QualityController::class,'updateConfirmInterview'])->name('updateToInterviewConfirmed');
    Route::get('reject-cv/{id}/{viewString}',[App\Http\Controllers\Administrator\QualityController::class ,'updateCVReject'])->name('updateToRejectedCV');
    Route::get('all-applicants-reject-cv-list', [App\Http\Controllers\Administrator\QualityController::class ,'getAllApplicantWithRejectedCv'])->name('applicantWithRejectedCV');
    Route::get('get-reject-cv-applicants',  [App\Http\Controllers\Administrator\QualityController::class ,'getRejectCVClients']);
    Route::POST('revert-quality-cv', [App\Http\Controllers\Administrator\QualityController::class ,'revertQualityCv'])->name('revertQualityCv');
    Route::get('cleared-applicants-cv', [App\Http\Controllers\Administrator\QualityController::class ,'getAllApplicantsWithConfirmedInterview'])->name('applicantsWithConfirmedInterview');
    Route::get('get-confirm-cv-applicants', [App\Http\Controllers\Administrator\QualityController::class ,'getConfirmCVApplicants'])->name('get-confirm-cv-applicants');
    Route::POST('revert-cv-quality/{applicant_cv_id}', [App\Http\Controllers\Administrator\QualityController::class ,'revert_cv_in_quality'])->name('revertInQuality');
    Route::get('applicant-cv-to-quality/{applicant_cv_id}',[App\Http\Controllers\Administrator\ApplicantController::class ,'getApplicantCvSendToQuality'])->name('sendCV');


    //crm routes
    Route::get('/sent_cv', [App\Http\Controllers\Administrator\CrmController::class ,'sentCv'])->name('sent_cv');
    Route::get('/crm-sent-cv', [App\Http\Controllers\Administrator\CrmController::class ,'crmSentCv'])->name('crmSentCv');
    Route::get('/crm-notes/{crm_applicant_id}/{crm_sale_id}', [App\Http\Controllers\Administrator\CrmController::class ,'getCrmNotesDetails'])->name('viewAllCrmNotes');
    Route::POST('process-sent-cv', [App\Http\Controllers\Administrator\CrmController::class ,'store'])->name('processCv');
    Route::get('/qualified_staff_cv', [App\Http\Controllers\Administrator\CrmController::class ,'qualifiedStaff'])->name('qualified_staff_cv');
    Route::get('/non-qualified_staff_cv', [App\Http\Controllers\Administrator\CrmController::class ,'nonQualifiedStaff'])->name('non-qualified_staff_cv');
    Route::get('/crm-notes/{crm_applicant_id}/{crm_sale_id}/datatable', [App\Http\Controllers\Administrator\CrmController::class ,'getCrmNotesDataTable'])->name('viewAllCrmNotesDatatable');

    Route::get('/crm-sent-cv-nurse', [App\Http\Controllers\Administrator\CrmController::class ,'crmSentCvNurse'])->name('crmSentCvNurse');
    Route::get('/crm-sent-cv-non-nurse', [App\Http\Controllers\Administrator\CrmController::class ,'crmSentCvNonNurse'])->name('crm-sent-cv-non-nurse');
    Route::get('/crm-reject-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'rejectCv'])->name('crm-reject-cvs');
    Route::get('/crm-reject-cv', [App\Http\Controllers\Administrator\CrmController::class ,'crmRejectCv'])->name('crm-reject-cv');
    Route::get('/crm-request_cv', [App\Http\Controllers\Administrator\CrmController::class ,'requestNurse'])->name('crm-request_cv');
    Route::get('/crm-request_nurse_cv', [App\Http\Controllers\Administrator\CrmController::class ,'crmRequestNurse'])->name('crm-request_nurse_cv');
    Route::POST('schedule-interview', [App\Http\Controllers\Administrator\CrmController::class ,'getInterviewSchedule'])->name('scheduleInterview');
    Route::get('/crm-request_non_qualified-cv', [App\Http\Controllers\Administrator\CrmController::class ,'requestNonNurse'])->name('crm-request_non-qualified-cv');
    Route::get('/crm-request_nonQualified-cv', [App\Http\Controllers\Administrator\CrmController::class ,'crmRequestNonNurse'])->name('crm-request_nonQualified-cv');
    Route::get('/crm-reject-request', [App\Http\Controllers\Administrator\CrmController::class ,'rejectRequestCv'])->name('crm-reject-request');
    Route::get('/crm-reject-by-request', [App\Http\Controllers\Administrator\CrmController::class ,'crmRejectByRequest'])->name('crm-reject-by-request');
    Route::get('/crm-confirmation_cv', [App\Http\Controllers\Administrator\CrmController::class ,'crmConfirmCv'])->name('crm-confirmation_cv');
    Route::get('/crm-confirmation', [App\Http\Controllers\Administrator\CrmController::class ,'crmConfirmation'])->name('crm-confirmation');
    Route::get('/crm-rebook_cv', [App\Http\Controllers\Administrator\CrmController::class ,'crmRebook'])->name('crmRebook');
    Route::get('/crm-rebook', [App\Http\Controllers\Administrator\CrmController::class ,'crmRebookCv'])->name('crm-rebook');
    Route::post('/rebook-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'rebookAction'])->name('rebookAction');
    Route::get('/crm-pre-start', [App\Http\Controllers\Administrator\CrmController::class ,'crmPreStartCv'])->name('crm-pre-start');
    Route::get('/crm-pre-start-date', [App\Http\Controllers\Administrator\CrmController::class ,'crmPreStartDate'])->name('crmPreStartDate');
    Route::post('/attended-to-pre-start-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'attendedToPreStartAction'])->name('attendedToPreStartAction');
    Route::get('/crm-declined-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmDeclinedCv'])->name('crm-declined-cvs');
    Route::get('/crm-declined', [App\Http\Controllers\Administrator\CrmController::class ,'crmDeclined'])->name('crm-declined');
    Route::post('/declined-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'declinedAction'])->name('declinedAction');
    Route::get('/crm-not-attended-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmNotAttendedCv'])->name('crmNotAttendedCvs');
    Route::get('/crm-not-attended', [App\Http\Controllers\Administrator\CrmController::class ,'crmNotAttended'])->name('crmNotAttended');
    Route::get('/crm-start-date', [App\Http\Controllers\Administrator\CrmController::class ,'crmStartDataCV'])->name('crm-start-date');
    Route::get('/crm-start-date-cv', [App\Http\Controllers\Administrator\CrmController::class ,'crmStartDate'])->name('crmStartDate');
    Route::post('/start-date-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'startDateAction'])->name('startDateAction');
    Route::get('/crm-start-date-hold', [App\Http\Controllers\Administrator\CrmController::class ,'crmStartDateHold'])->name('crmStartDateHold');
    Route::get('/crm-start-date-hold-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmStartDateHoldCv'])->name('crmStartDateHoldCvs');
    Route::post('/start-date-hold-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'startDateHoldAction'])->name('startDateHoldAction');
    Route::get('/crm-invoice-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmInvoiceCv'])->name('crmInvoiceCvs');
    Route::get('/crm-invoice', [App\Http\Controllers\Administrator\CrmController::class ,'crmInvoice'])->name('crmInvoice');
//    Route::get('/crm-sent-invoice-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmInvoiceSentCv'])->name('crmSentInvoiceCvs');
//    Route::get('/crm-invoice_final', [App\Http\Controllers\Administrator\CrmController::class ,'crmInvoiceFinalSent']);

    Route::post('/invoice-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'invoiceAction'])->name('invoiceAction');
    Route::get('/crm-invoice-sent-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmInvoiceSentCv'])->name('crmInvoiceSentCvs');
    Route::get('/crm-invoice-final-sent', [App\Http\Controllers\Administrator\CrmController::class ,'crmInvoiceFinalSent'])->name('crm-invoice-final-sent');
    Route::post('/invoice-action-sent', [App\Http\Controllers\Administrator\CrmActionController::class ,'invoiceActionSent'])->name('invoiceActionSent');
    Route::get('/crm-dispute-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmDisputeCV'])->name('crmDisputeCvs');
    Route::get('/crm-dispute', [App\Http\Controllers\Administrator\CrmController::class ,'crmDispute'])->name('crmDispute');
    Route::post('/dispute-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'disputeAction'])->name('disputeAction');

    Route::get('/crm-paid-cvs', [App\Http\Controllers\Administrator\CrmController::class ,'crmPaidCV'])->name('crmPaidCvs');
    Route::get('/crm-paid', [App\Http\Controllers\Administrator\CrmController::class ,'crmPaid'])->name('crmPaid');
    Route::post('/paid-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'paidAction'])->name('paidAction');
    Route::post('/reject-by-request-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'rejectByRequestAction'])->name('rejectByRequestAction');
    Route::post('revert-sent-cv-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'revertSentCvAction'])->name('revertSentCvAction');

    Route::get('post-code-finder',  [App\Http\Controllers\Administrator\PostcodeController::class ,'index'])->name('postcodeFinder');
    Route::post('post-code-search-results',  [App\Http\Controllers\Administrator\PostcodeController::class ,'getPostcodeResults'])->name('postcodeFinderResults');
    Route::post('sent-cv-action', [App\Http\Controllers\Administrator\CrmActionController::class ,'sentCvAction'])->name('sentCvAction');


});




/*==========a====================================Artisan routes here============================================= */
Route::get('artisan-login', [App\Http\Controllers\ArtisanCommandController::class, 'configurationPassword'])->middleware('artisan_view');
Route::post('artisanlogin', [App\Http\Controllers\ArtisanCommandController::class, 'checkConfigurationPassword'])->name('artisanlogin');
Route::get('php_artisan_cmd', [App\Http\Controllers\ArtisanCommandController::class, 'runCommand'])->middleware('artisan_view');
Route::get('artisan', [App\Http\Controllers\ArtisanCommandController::class, 'indexArtisan'])->middleware(['artisan_view', 'artisan']);
Route::get('artisan-logout', [App\Http\Controllers\ArtisanCommandController::class, 'configurationLogout'])->name('artisanLogout')->middleware('artisan_view');
