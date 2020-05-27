<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'revalidate']], function () {
    Route::get('branch/select', 'DashboardController@branch');
    Route::post('branch/select', 'DashboardController@branchStore');

    Route::group(['middleware' => 'branch'], function () {
        Route::get('/', 'DashboardController@index')->name('home');

        Route::get('permission-denied', 'DashboardController@permissionDenied')->name('permission-denied');

        //users pages
        Route::get('web-registrations', 'UserController@webRegistrations')->name('web-registrations.index');
        Route::get('users', 'UserController@index')->name('users.index');
        Route::get('users/create', 'UserController@create')->name('users.create');
        Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
        Route::get('users/excel', 'UserController@getUsersExcel')->name('users.excel');
        Route::get('users/{user}', 'UserController@infoShow')->name('users.show');
        Route::get('cockpit', 'UserController@cockpit')->name('cockpit.index');

        Route::get('wallet', 'WalletController@index')->name('wallet.index');


        //loans pages
        Route::get('loan-applications', 'LoanController@index')->name('loans.index');
        Route::get('loan-applications/to-assign', 'LoanController@assign')->name('loans.assign');
        Route::get('loan-applications/my-clients', 'LoanController@myClients')->name('loans.my_clients');
        Route::get('loan-applications/excel', 'LoanController@loansApplicationExcel')->name('loans.excel');
        Route::get('loan-applications/{loan}', 'LoanController@show')->name('loans.show');
        Route::get('loan-applications/{loan}/history', 'LoanController@loanHistory')->name('loans.calculation-history');

        //pay bills
        Route::get('pay-bills', 'PaybillController@index')->name('merchants.index');
        Route::get('pay-bills/create', 'PaybillController@create')->name('merchants.create');
        Route::get('pay-bills/{user}/edit', 'PaybillController@edit')->name('merchants.edit');
        Route::get('pay-bills/{user}', 'PaybillController@infoShow')->name('merchants.show');

        //merchants
        Route::get('merchants', 'MerchantController@index')->name('merchants1.index');
        Route::get('merchants/create', 'MerchantController@create')->name('merchants1.create');
        Route::get('merchants/{merchant}/edit', 'MerchantController@edit')->name('merchants1.edit');
        Route::get('merchants/payments', 'MerchantController@payments')->name('merchants1.payments');
        Route::get('merchants/reconciliations', 'MerchantController@reconciliations')->name('merchants1.reconciliations');

        //dailyturnover
        Route::get('daily-turnover/day-open', 'DailyTurnoverController@dayopenIndex')->name('daily-turnover.day-open');

        Route::get('daily-turnover/audit-report', 'DailyTurnoverController@auditIndex')->name('daily-turnovers.audit');

        //credits
        Route::get('credits', 'CreditController@index')->name('credits.index');

        //messages
        Route::get('push-notifications', 'PushNotificationController@index')->name('push-notifications.index');

        Route::get('messages', 'MessageController@index')->name('messages.index');

        //masters
        Route::get('relationships', 'RelationshipController@index')->name('relationships.index');

        Route::get('loan-reasons', 'LoanReasonController@index')->name('loan-reasons.index');

        Route::get('loan-decline-reasons', 'LoanDeclineReasonController@index')->name('loan-decline-reasons.index');

        Route::get('loan-onhold-reasons', 'LoanOnholdReasonController@index')->name('loan-onhold-reasons.index');

        Route::get('loan-types', 'LoanTypeController@index')->name('loan-types.index');

        Route::get('existing-loan-types', 'ExistingLoanTypeController@index')->name('existing-loan-types.index');

        Route::get('countries', 'CountryController@index')->name('countries.index');

        Route::get('districts', 'DistrictController@index')->name('districts.index');

        Route::get('branches', 'BranchController@index')->name('branches.index');

        Route::get('templates', 'TemplateController@index')->name('templates.index');

        Route::get('banks', 'BankController@index')->name('banks.index');

        Route::get('nlb-reasons', 'NlbReasonController@index')->name('nlb-reasons.index');

        Route::get('nlbs', 'NlbController@index')->name('nlbs.index');

        Route::get('bank-reconciliation', 'BankReconcileController@index')->name('bank.reconcile');

        Route::get('audit-bank-reconciliation', 'BankReconcileController@audit')->name('audit.reconcile');

        Route::get('referral-categories', 'ReferralCategoryController@index')->name('referral-categories.index');

        Route::get('raffle-winners', 'RaffleWinnerController@index')->name('raffle-winners.index');

        Route::get('referral-histories', 'ReferralController@index')->name('referral-histories.index');

        Route::group(['prefix' => 'ajax'], function () {
            //users related routes
            Route::get('datatable-users', 'UserController@indexDatatable');

            Route::post('users/emails/{email_info}/store', 'UserController@savePrimaryEmail');

            Route::get('countries/{country}/data', 'UserController@countryData');

            Route::get('users/{user}/edit', 'UserController@show');
            Route::post('users', 'UserController@store');
            Route::delete('users/{user}', 'UserController@destroy');

            Route::delete('documents/{document}', 'UserController@documentDelete');

            Route::get('users/{user}/works', 'UserController@worksInfo');
            Route::post('users/{user}/works', 'UserController@workStore');
            Route::post('users/{user}/working-type', 'UserController@workingTypeStore');
            Route::get('users/{user}/works/{work}/edit', 'UserController@worksEdit');
            Route::delete('users/{user}/works/{work}', 'UserController@worksDelete');
            Route::post('users/country-pdf', 'UserController@countryPdf');
            Route::get('users/{user}/loan-types', 'UserController@loanTypes');

            Route::get('users/{user}/resend/{id}', 'UserController@resendVerificationMail');

            Route::get('users/{user}/country-banks', 'UserController@userCountryBanks');
            Route::get('users/{user}/banks', 'UserController@userBanks');
            Route::post('users/{user}/banks', 'UserController@userBanksStore');

            Route::get('users/{user}/references', 'UserController@userReferences');
            Route::post('users/{user}/references', 'UserController@userReferencesStore');

            Route::get('users/datatable-wallets', 'UserController@walletDatatable');
            Route::post('users/{user}/wallet', 'UserController@walletStore');
            Route::post('users/{user}/wallet-total', 'UserController@getWallet');

            Route::post('users/{user}/referral-status', 'UserController@referralStatus');

            Route::post('cockpit', 'UserController@cockpitData');
            Route::post('cockpit/export', 'UserController@cockpitExport');

            Route::get('datatable-wallet', 'WalletController@indexDatatable');
            Route::post('wallets', 'WalletController@store');

            //pay bills
            Route::post('pay-bills', 'PaybillController@store');
            Route::get('pay-bills/{user}/edit', 'PaybillController@show');
            Route::delete('pay-bills/{user}', 'PaybillController@destroy');
            Route::get('datatable-pay-bills', 'PaybillController@indexDatatable');


            //merchants
            Route::post('merchants', 'MerchantController@store');
            Route::post('merchants/reconciliations', 'MerchantController@reconciliationStore');
            Route::get('merchants/reconciliations/{reconciliation}/edit', 'MerchantController@reconciliationEdit');
            Route::get('merchants/reconciliations/{reconciliation}/history', 'MerchantController@reconciliationHistory');
            Route::delete('merchants/reconciliations/{reconciliation}', 'MerchantController@reconciliationDelete');
            Route::get('merchants/datatable-reconciliations', 'MerchantController@reconciliationDatatable');
            Route::get('merchants/{merchant}/edit', 'MerchantController@show');
            Route::delete('merchants/{merchant}', 'MerchantController@destroy');
            Route::get('datatable-merchants', 'MerchantController@indexDatatable');
            Route::get('merchants/{merchant}/branches', 'MerchantController@branches');
            Route::get('merchants/{merchant}/resend/{id}', 'MerchantController@resendVerificationMail');
            Route::get('datatable-merchant-transactions', 'MerchantController@merchantTransactionsDatatable');

            Route::post('merchants/emails/{email_info}/store', 'MerchantController@savePrimaryEmail');

            Route::get('datatable-merchants-payments', 'MerchantController@paymentsDatatable');
            Route::post('payments/export', 'MerchantController@exportPayment');

            //profile related routes
            Route::get('profile', 'UserController@profileInfo');
            Route::post('profile', 'UserController@profileStore');
            Route::post('profile/email', 'UserController@changeEmail');
            Route::post('profile/password', 'UserController@changePassword');
            Route::post('profile/country', 'UserController@changeCountry');
            Route::delete('profile-pic', "UserController@profilePicDelete");

            //loans related routes
            Route::get('datatable-loans', 'LoanController@indexDatatable');
            Route::get('users/{user}/loans-master', 'LoanController@loansMasterData');
            Route::get('loan-type/{type}', 'LoanController@loanTypeInfo');
            Route::post('loans', 'LoanController@store');
            Route::get('loans/{loan}/edit', 'LoanController@edit');
            Route::delete('loans/{loan}', 'LoanController@destroy');
            Route::post('loans/{loan}/status/{status}', 'LoanController@changeStatus');
            Route::get('loans/{loan}/notes', 'LoanController@notesListing');
            Route::get('loans/{loan}/notes/{note}', 'LoanController@notesEdit');
            Route::delete('loans/{loan}/notes/{note}', 'LoanController@loanNotesDestroy');
            Route::post('loans/{loan}/notes', 'LoanController@notesStore');
            Route::get('loans/{loan}/datatable-notes', 'LoanController@notesDatatable');
            Route::get('loans/{loan}/transactions', 'LoanController@transactionDatatable');
            Route::get('loans/{loan}/user-branches', 'LoanController@loanUserBranches');
            Route::post('loans/{loan}/transactions', 'LoanController@saveTransaction');
            Route::post('assign-employee', 'LoanController@assignEmployee');

            Route::get('loans/{loan}/history', 'LoanController@loanLastCalculationHistory');
            Route::get('history/{history}/edit', 'LoanController@ajaxHistoryEdit');
            Route::post('history/{history}', 'LoanController@calculationHistoryUpdate');
            Route::post('history/{history}/receipt', 'LoanController@calculationHistoryReceiptDownload');
            Route::delete('history/{history}', 'LoanController@ajaxHistoryDelete');

            Route::get('loans/{loan}/status-history', 'LoanController@loanStatusHistory');

            //dailyturnover
            Route::get('daily-turnover/day-open/create', 'DailyTurnoverController@dayopenCreate');
            Route::post('daily-turnover/day-open', 'DailyTurnoverController@dayopenStore');
            Route::get('daily-turnover/datatable-day-open', 'DailyTurnoverController@dayOpenDatatable');
            Route::get('countries/{country}/branch', 'DailyTurnoverController@branches');
            Route::get('daily-turnover/day-open/{date}/{user}/{branch}', 'DailyTurnoverController@dayOpenEdit');

            Route::get('daily-turnover/datatable-audit-report', 'DailyTurnoverController@auditDatatable');
            Route::get('daily-turnover/audit-report/{date}/{user}/{branch}', 'DailyTurnoverController@auditShow');
            Route::post('daily-turnover/audit-report/{date}/{user}/{branch}/approve', 'DailyTurnoverController@auditApprove');

            Route::get('bank-reconciliation-data', 'BankReconcileController@indexDatatable');
            Route::post('bank-reconcile', 'BankReconcileController@reconcile');
            Route::post('audit-bank-reconciliation', 'BankReconcileController@auditData');

            //credits
            Route::get('datatable-credits', 'CreditController@indexDatatable');
            Route::post('credits', 'CreditController@store');
            Route::get('credits/{credit}/edit', 'CreditController@show');
            Route::post('credits/status', 'CreditController@statusCredits');
            Route::get('credits/{credit}/history', 'CreditController@statusHistory');
            Route::get('credits/{credit}/wallet', 'CreditController@walletHistory');
            Route::post('credits/csv', 'CreditController@csvExport');
            Route::delete('credits/{credit}', 'CreditController@destroy');

            Route::post('messages', 'MessageController@store');
            Route::get('datatable-messages', 'MessageController@indexDatatable');

            Route::post('push-notifications', 'PushNotificationController@store');
            Route::get('datatable-push-notifications', 'PushNotificationController@indexDatatable');

            Route::get('countries/{country}/users', 'MessageController@getUsersFromCountry');

            //nlbs
            Route::get('datatable-nlbs', 'NlbController@indexDatatable');
            Route::post('nlbs', 'NlbController@store');
            Route::get('nlbs/{nlb}/edit', 'NlbController@edit');
            Route::delete('nlbs/{nlb}', 'NlbController@destroy');

            //master tables
            Route::get('datatable-relationships', 'RelationshipController@indexDatatable');
            Route::post('relationships', 'RelationshipController@store');
            Route::get('relationships/{relationship}/edit', 'RelationshipController@edit');
            Route::delete('relationships/{relationship}', 'RelationshipController@destroy');

            Route::get('datatable-loan-reasons', 'LoanReasonController@indexDatatable');
            Route::post('loan-reasons', 'LoanReasonController@store');
            Route::get('loan-reasons/{reason}/edit', 'LoanReasonController@edit');
            Route::delete('loan-reasons/{reason}', 'LoanReasonController@destroy');

            Route::get('datatable-loan-decline-reasons', 'LoanDeclineReasonController@indexDatatable');
            Route::post('loan-decline-reasons', 'LoanDeclineReasonController@store');
            Route::get('loan-decline-reasons/{reason}/edit', 'LoanDeclineReasonController@edit');
            Route::delete('loan-decline-reasons/{reason}', 'LoanDeclineReasonController@destroy');

            Route::get('datatable-loan-onhold-reasons', 'LoanOnholdReasonController@indexDatatable');
            Route::post('loan-onhold-reasons', 'LoanOnholdReasonController@store');
            Route::get('loan-onhold-reasons/{reason}/edit', 'LoanOnholdReasonController@edit');
            Route::delete('loan-onhold-reasons/{reason}', 'LoanOnholdReasonController@destroy');

            Route::get('datatable-loan-types', 'LoanTypeController@indexDatatable');
            Route::post('loan-types', 'LoanTypeController@store');
            Route::get('loan-types/{type}/edit', 'LoanTypeController@edit');
            Route::delete('loan-types/{type}', 'LoanTypeController@destroy');

            Route::get('datatable-existing-loan-types', 'ExistingLoanTypeController@indexDatatable');
            Route::post('existing-loan-types', 'ExistingLoanTypeController@store');
            Route::get('existing-loan-types/{type}/edit', 'ExistingLoanTypeController@edit');
            Route::delete('existing-loan-types/{type}', 'ExistingLoanTypeController@destroy');

            Route::get('datatable-countries', 'CountryController@indexDatatable');
            Route::post('countries', 'CountryController@store');
            Route::get('countries/{country}/edit', 'CountryController@edit');
            Route::delete('countries/{country}', 'CountryController@destroy');

            Route::get('datatable-districts', 'DistrictController@indexDatatable');
            Route::post('districts', 'DistrictController@store');
            Route::get('districts/{district}/edit', 'DistrictController@edit');
            Route::delete('districts/{district}', 'DistrictController@destroy');

            Route::get('datatable-branches', 'BranchController@indexDatatable');
            Route::post('branches', 'BranchController@store');
            Route::get('branches/{branch}/edit', 'BranchController@edit');
            Route::delete('branches/{branch}', 'BranchController@destroy');

            Route::get('datatable-templates', 'TemplateController@indexDatatable');
            Route::post('templates', 'TemplateController@store');
            Route::get('templates/{template}/edit', 'TemplateController@edit');

            Route::get('datatable-referral-categories', 'ReferralCategoryController@indexDatatable');
            Route::post('referral-categories', 'ReferralCategoryController@store');
            Route::get('referral-categories/{category}/edit', 'ReferralCategoryController@edit');
            Route::delete('referral-categories/{category}', 'ReferralCategoryController@destroy');

            Route::get('datatable-banks', 'BankController@indexDatatable');
            Route::post('banks', 'BankController@store');
            Route::get('banks/{bank}/edit', 'BankController@edit');
            Route::delete('banks/{bank}', 'BankController@destroy');

            Route::get('datatable-nlb-reasons', 'NlbReasonController@indexDatatable');
            Route::post('nlb-reasons', 'NlbReasonController@store');
            Route::get('nlb-reasons/{reason}/edit', 'NlbReasonController@edit');
            Route::delete('nlb-reasons/{reason}', 'NlbReasonController@destroy');
            Route::get('nlb-reasons/{type}/types', 'NlbReasonController@typeReasonsListing');

            Route::get('datatable-raffle-winners', 'RaffleWinnerController@indexDatatable');

            Route::get('datatable-referral-histories', 'ReferralController@indexDatatable');
            Route::post('referral-histories/excel', 'ReferralController@excelDownload');

            Route::post('dashboard/data', 'DashboardController@getData');
            Route::post('dashboard/data/total', 'DashboardController@totalData');
            Route::post('dashboard/data/excel', 'DashboardController@totalExcel');
            Route::post('dashboard/history/excel', 'DashboardController@historyExcel');
            Route::post('dashboard/cash-data/pdf', 'DashboardController@cashDataPdf');
        });
    });
});
