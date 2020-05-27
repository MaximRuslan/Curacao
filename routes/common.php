<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Common', 'middleware' => ['revalidate']], function () {
    Route::get('user/{user}', 'UserController@infoShow')->name('users.info');
    Route::resource('/users', 'UserController');
    Route::post('/users-data', ['as' => 'users-data', 'uses' => 'UserController@getList']);

    Route::post('/loan-application-data', [
        'as'   => 'loan-application-data',
        'uses' => 'LoanApplicationController@getList'
    ]);

    Route::post('/transactions/{id}', [
        'as'   => 'get.transactions',
        'uses' => 'LoanApplicationController@getTransactions'
    ]);

    Route::post('/save-transaction', [
        'as'   => 'save-transaction',
        'uses' => 'LoanApplicationController@saveTransaction'
    ]);

    Route::delete('loan-applications/notes/{note}', 'LoanApplicationController@loanNotesDestroy')->name('loan-notes.destroy');

    Route::get('loan-applications/{loan}/history', 'LoanApplicationController@loanHistory')->name('loan-applications.calculation-history');

    Route::resource('/loan-applications', 'LoanApplicationController');

    Route::post('getTerritory', ['as' => 'get.territory.address', 'uses' => 'LoanApplicationController@getTerritory']);
    Route::post('get-loan-type-info', 'LoanApplicationController@getLoanTypeInfo')->name('get-loan-type-info');

    Route::group(['prefix' => 'ajax'], function () {
        Route::post('users', "UserController@ajaxStore");
        Route::post('users/{user}', "UserController@ajaxUpdate");

        Route::post('users/{user}/working-type', "UserController@workingType");

        Route::post('users/{user}/wallet', "UserController@ajaxWalletAdd");
        Route::post('wallets-data', 'UserController@walletDatatable');
        Route::post('get-wallet/{user}', "UserController@getWallet");

        Route::get('users/{user}/works', "UserController@ajaxWorksGet");
        Route::post('users/{user}/works', "UserController@ajaxWorksStore");
        Route::get('users/{user}/works/{work}/edit', 'UserController@ajaxWorksEdit');
        Route::delete('users/{user}/works/{work}', 'UserController@ajaxWorksDelete');

        Route::delete('documents/{document}', 'UserController@ajaxDeleteDocument');

        Route::get('users/banks', 'UserController@ajaxUserTerritoryBanks');
        Route::get('users/{user}/banks', 'UserController@ajaxBanksGet');
        Route::post('users/{user}/banks', 'UserController@ajaxBanksUpdate');
        Route::post('users/{user}/banks-list', "UserController@ajaxUserBanks");

        Route::get('users/{user}/references', 'UserController@ajaxReferencesGet');
        Route::post('users/{user}/references', 'UserController@ajaxReferencesUpdate');

        Route::post('profile/email', 'UserController@changeEmail');

        Route::get('loan-applications/{loan}/history', 'LoanApplicationController@ajaxHostory');
        Route::get('loan-applications/{loan}/status-history', 'LoanApplicationController@ajaxStatusHistory');
        Route::get('loan-applications/{loan}/notes', 'LoanApplicationController@notesListing');
        Route::get('loan-applications/{loan}/notes/{note}', 'LoanApplicationController@notesEdit');
        Route::post('loan-applications/{loan}/notes', 'LoanApplicationController@notesStore');
        Route::get('loan-applications/{loan}/user-branches', 'LoanApplicationController@loanUserBranches');
        Route::get('transactions/{transaction}', 'LoanApplicationController@transactionDetail');
        Route::post('transactions/{transaction}', 'LoanApplicationController@transactionUpdate');
        Route::get('history/{history}/edit', 'LoanApplicationController@ajaxHistoryEdit');
        Route::post('history/{history}', 'LoanApplicationController@ajaxHistoryUpdate');
        Route::post('validate-loan-form', "LoanApplicationController@validateLoanForm");
    });
});
