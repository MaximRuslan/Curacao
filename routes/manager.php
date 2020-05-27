<?php
Route::group(['prefix' => 'manager', 'namespace' => 'Manager'], function () {
    Route::get('/dashboard', ['as' => 'manager.dashboard', 'uses' => 'DashboardController@index']);
});