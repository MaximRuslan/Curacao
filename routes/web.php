<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    Route::get('/', 'Client1\LoginController@showLoginForm')->name('login');
});


Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

Route::get('resend/{user}/verification', 'HomeController@resendVerificationMail');


Route::group(['middleware' => "guest"], function () {
//    Auth::routes();
    Route::get('login', 'Admin1\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Admin1\LoginController@login')->name('login.store');
    Route::get('registration', 'Admin1\RegistrationController@index')->name('registration');
    Route::post('registration', 'Admin1\RegistrationController@store')->name('registration.store');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', 'Client1\LoginController@logout');
});
Route::get('permission-denied', 'PagesController@permissionDenied')->name('error.permission-denied');

Route::group(['middleware' => ['guest']], function () {
    Route::get('/verify/{id}/{info}/{email}', ['as' => 'verify.client', 'uses' => 'Client1\DashboardController@verifyClient']);
    Route::get('/verify/merchant/{id}/{info}/{email}', ['as' => 'verify.merchant', 'uses' => 'Client1\DashboardController@verifyMerchant']);
});

Route::get('lang-change/{lang}', 'HomeController@languageChange');


Route::get('loan-contract/{type}', 'Client1\DashboardController@loanContract');

Route::group(['middleware' => 'redirectRole'], function () {
    Route::get('terms-conditions', 'Client\DashboardController@terms')->name('client.terms');

    Route::post('ajax/accept-terms', 'Client\DashboardController@acceptTerms');
});

//Route::get('/profile', 'HomeController@userProfile')->name('user-profile');
//Route::post('/profile', 'HomeController@postUserProfile')->name('post-profile');
//Route::post('set-country', 'HomeController@setCountry');

Route::post('message/callback', 'Admin1\MessageController@getCallback');