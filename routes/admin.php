<?php

//use Illuminate\Support\Facades\Route;

//Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'revalidate'], function () {
//    Route::get('branch/select', 'DashboardController@branch');
//    Route::post('branch/select', 'DashboardController@branchStore');
//    Route::group(['middleware' => 'branch'], function () {
//        Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'DashboardController@index']);

//        Route::resource('user-types', 'UserType');
//        Route::resource('user-department', 'UserDepartmentController');
//        Route::resource('relationship', 'RelationshipController');
//        Route::resource('loan-reasons', 'LoanReasonController');
//        Route::resource('loan-decline-reasons', 'LoanDeclineReasonController');
//        Route::resource('loan-onhold-reasons', 'LoanOnHoldReasonController');
//        Route::resource('loan-types', 'LoanTypeController');
//        Route::resource('existing-loan-type', 'ExistingLoanTypeController');
//        Route::resource('countries', 'CountryController');
//        Route::resource('user-territory', 'UserTerritoryController');
//        Route::resource('branch', 'BranchController');
//        Route::resource('banks', 'BankController');

//        Route::resource('credits', 'CreditController');

//        Route::resource('transaction-types', 'TransactionTypeController');
//
//        Route::resource('proof-types', 'ProofTypeController');




//        Route::resource('nlb-transactions', 'NlbController');

//        Route::resource('nlb-reasons', 'NLBReasonController');

//        Route::post('merchants/{merchant}', 'MerchantController@update');

//        Route::resource('merchants', 'MerchantController');

//        Route::post('user-types-data', ['as' => 'usertypes-data', 'uses' => 'UserType@getList']);

//        Route::post('user-department-data', [
//            'as'   => 'userdepartment-data',
//            'uses' => 'UserDepartmentController@getList'
//        ]);
//        Route::post('user-territory-data', [
//            'as'   => 'userterritory-data',
//            'uses' => 'UserTerritoryController@getList'
//        ]);
//
//        Route::post('loan-reasons-data', ['as' => 'loan-reasons-data', 'uses' => 'LoanReasonController@getList']);
//
//        Route::post('loan-decline-data', [
//            'as'   => 'loan-decline-data',
//            'uses' => 'LoanDeclineReasonController@getList'
//        ]);
//        Route::post('loan-onhold-data', ['as' => 'loan-onhold-data', 'uses' => 'LoanOnHoldReasonController@getList']);
//        Route::post('transaction-type-data', [
//            'as'   => 'transaction-type-data',
//            'uses' => 'TransactionTypeController@getList'
//        ]);
//
//        Route::post('loan-type-data', ['as' => 'loan-type-data', 'uses' => 'LoanTypeController@getList']);
//        Route::post('proof-type-data', ['as' => 'proof-type-data', 'uses' => 'ProofTypeController@getList']);
//        Route::post('existing-loan-type-data', [
//            'as'   => 'existing-loan-type-data',
//            'uses' => 'ExistingLoanTypeController@getList'
//        ]);
//
//        Route::post('banks-data', 'BankController@indexDatatable')->name('banks-data');
//        Route::post('relationship-data', 'RelationshipController@indexDatatable')->name('relationship-data');
//        Route::post('countries-data', 'CountryController@indexDatatable')->name('countries-data');
//        Route::get('countries/{country}/territories', 'CountryController@territories');
//        Route::get('countries/{country}/info', 'CountryController@info');
//        Route::get('countries/{country}/branch', 'CountryController@branches');
//        Route::post('merchants-data', 'MerchantController@indexDatatable')->name('merchants-data');
//        Route::post('branch-data', 'BranchController@indexDatatable')->name('branch-data');

//        Route::post('credits-data', 'CreditController@indexDatatable')->name('credits-data');
//        Route::post('credits/status', 'CreditController@statusCredits');
//        Route::get('credits/{credit}/history', 'CreditController@statusHistory');
//        Route::get('credits/{credit}/wallet', 'CreditController@walletHistory');

//        Route::get('cms/{type}', 'CmsController@index')->name('cms.index');
//        Route::post('cms/{type}', 'CmsController@update')->name('cms.update');

//        Route::get('daily-turnover/day-open', 'DailyTurnoverController@dayopenIndex')->name('daily-turnover.day-open');
//        Route::get('daily-turnover/day-open/create', 'DailyTurnoverController@dayopenCreate')->name('daily-turnover.day-open.create');
//        Route::post('daily-turnover/day-open', 'DailyTurnoverController@dayopenStore')->name('daily-turnovers.dayopen.store');
//        Route::post('daily-turnover/day-open-data', 'DailyTurnoverController@dayOpenDatatable')->name('daily-turnover.day-open.data');
//        Route::get('daily-turnover/day-open/{date}', 'DailyTurnoverController@dayOpenShow')->name('daily-turnover.day-open.show');
//        Route::get('daily-turnover/day-open/{date}/{user}/{branch}', 'DailyTurnoverController@dayOpenEdit')->name('daily-turnover.day-open.edit');

//
//        Route::get('daily-turnover/audit-report', 'DailyTurnoverController@auditIndex')->name('daily-turnovers.audit.index');
//        Route::post('daily-turnover/audit-report-data', 'DailyTurnoverController@auditDatatable')->name('daily-turnovers.audit.data');
//        Route::get('daily-turnover/audit-report/{date}/{user}/{branch}', 'DailyTurnoverController@auditShow')->name('daily-turnovers.audit.show');
//        Route::post('daily-turnover/audit-report/{date}/{user}/{branch}/approve', 'DailyTurnoverController@auditApprove')->name('daily-turnovers.audit.approve');

//        Route::get('messages', 'MessageController@index')->name('messages.index');
//        Route::post('messages', 'MessageController@store')->name('messages.store');
//        Route::post('messages-data', 'MessageController@indexDatatable')->name('messages-data');
//        Route::get('countries/{country}/users', 'MessageController@getUsersFromCountry');

//        Route::post('nlb-reasons-data', 'NLBReasonController@indexDatatable')->name('nlb-reasons-data');
//        Route::get('nlb-reasons/{type}/types', 'NLBReasonController@typeReasonsListing');

//        Route::post('nlb-transactions-data', 'NlbController@indexDatatable')->name('nlb-transactions-data');
//    });
//});