<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'client', 'namespace' => '\\Client'], function () {
        Route::post('login', 'LoginController@store');
        Route::post('forgot-password', "LoginController@forgotPassword");

        Route::group(['middleware' => 'jwt.auth'], function () {
            /**
             * @desc user related routes
             * @date 29 Jun 2018 15:51
             */
            Route::get('user-info', 'UserController@userInfo');
            Route::post('change-password', 'UserController@changePassword');
            Route::post('update-profile', 'UserController@update');
            Route::post('accept-terms', 'UserController@acceptTerms');

            /**
             * @desc Loan related apis
             * @date 29 Jun 2018 15:51
             */
            Route::resource('loan-applications', 'LoanApplicationController');
            Route::post('loan-types/{loan_type}', 'LoanApplicationController@loanTypeInfo');


            /**
             * @desc notifications route
             * @date 11 Jul 2018 13:13
             */

            Route::get('messages', 'MessageController@index');

            /**
             * @desc credit related apis
             * @date 29 Jun 2018 15:52
             */
            Route::resource('credits', 'CreditController');

            /**
             * @desc logout relates api
             * @date 07 Jul 2018 14:50
             */
            Route::delete('logout', 'LoginController@destroy');

            /**
             * @desc device token related api
             * @date 07 Jul 2018 15:13
             */
            Route::post('device-token', 'LoginController@tokenChange');

            Route::get('referrals', 'ReferralController@index');
            Route::get('referral-histories', 'ReferralController@history');
        });
    });
});