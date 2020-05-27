<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest_merchant'], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('login.index');
    Route::post('login', 'LoginController@login')->name('login.store');
    Route::get('resend/{merchant}/verification', 'LoginController@resendVerificationMail');

    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
});

Route::group(['middleware' => ['auth_merchant', 'revalidate']], function () {
    Route::get('logout', 'LoginController@logout')->name('logout');

    Route::get('/', 'HomeController@index')->name('home.index');

    Route::get('payments', 'PaymentController@index')->name('payments.index');

    Route::get('reconciliations', 'ReconciliationController@index')->name('reconciliations.index');

    Route::group(['prefix' => 'ajax'], function () {
        Route::post('profile/password', 'HomeController@changePassword');


        Route::get('datatable-payments', 'PaymentController@indexDatatable');

        Route::post('branches/{branch}', 'HomeController@branch');

        Route::post('users-loans/{id}', 'PaymentController@userLoan');

        Route::post('payments', 'PaymentController@store');

        Route::get('datatable-reconciliations', 'ReconciliationController@indexDatatable');
        Route::post('reconciliations', 'ReconciliationController@approve');
        Route::get('reconciliations/{reconciliation}/history', 'ReconciliationController@history');
    });
});