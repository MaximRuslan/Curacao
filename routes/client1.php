<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login')->name('login.store');
});

Route::group(['middleware' => ['auth', 'role:client', 'firstLogin', 'revalidate']], function () {
    Route::get('/', 'DashboardController@index')->name('home');

    Route::get('loans', 'LoanController@index')->name('loans.index');
    Route::get('loans/create', 'LoanController@create')->name('loans.create');
    Route::post('loans', 'LoanController@store')->name('loans.store');
    Route::get('loans/{loan}', 'LoanController@show')->name('loans.show');
    Route::get('loans/{loan}/calculations', 'LoanController@calculationShow')->name('loans.calculation');

    Route::get('credits', 'CreditController@index')->name('credits.index');

    Route::get('referrals', 'ReferralController@index')->name('referrals.index');

    Route::group(['prefix' => 'ajax', 'as' => 'ajax.'], function () {
        Route::get('profile', 'UserController@profileInfo');
        Route::post('profile', 'UserController@profileStore');
        Route::post('profile/email', 'UserController@changeEmail');
        Route::post('profile/password', 'UserController@changePassword');
        Route::post('profile/language/{lang}', 'UserController@changeLanguage');
        Route::delete('profile-pic', "UserController@profilePicDelete");

        Route::get('datatable-loans', 'LoanController@indexDatatable');
        Route::get('datatable-transactions/{loan}', 'LoanController@transactionDatatable');
        Route::get('loans/{loan}', 'LoanController@edit');
        Route::delete('loans/{loan}', 'LoanController@destroy');
        Route::get('loans/{loan}/history', 'LoanController@getLoanHistory');
        Route::post('loans/{loan}/history/{history}/receipt', 'LoanController@getTransactionReceipt');

        Route::get('loan-type/{type}', 'LoanController@loanTypeInfo');

        Route::get('datatable-referral-histories', 'ReferralController@indexDatatable');

        Route::get('datatable-credits', 'CreditController@indexDatatable');
        Route::post('credits', 'CreditController@store');
        Route::get('credits/{credit}/edit', 'CreditController@edit');
        Route::delete('credits/{credit}', 'CreditController@destroy');
    });
});