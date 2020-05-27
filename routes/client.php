<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
//    'prefix'     => LaravelLocalization::getCurrentLocale(),
    'namespace'  => 'Client',
    'middleware' => [
//        'localeSessionRedirect',
//        'localizationRedirect',
        'revalidate',
        'redirectRole',
        'firstLogin'
    ]
], function () {

    Route::get('/client-dashboard', ['as' => 'client.dashboard', 'uses' => 'DashboardController@index']);

    Route::get('loans', 'DashboardController@loans')->name('client.loans');

    Route::post('/loan-applications', ['as' => 'client.loan.application', 'uses' => 'DashboardController@getList']);

    Route::group(['middleware' => ['statusBasedFilter']], function () {

        Route::get('loans/create', 'DashboardController@create')->name('client.loans.create');

        Route::get('credits', 'CreditController@index')->name('client.credits.index');

        Route::post('credits', 'CreditController@store')->name('client.credits.store');

        Route::get('credits/{credit}', 'CreditController@show')->name('client.credits.show');

        Route::delete('credits/{credit}', 'CreditController@destroy')->name('client.credits.destroy');

        Route::post('credits-data', 'CreditController@indexDatatable')->name('client.credits.data');
    });
});
