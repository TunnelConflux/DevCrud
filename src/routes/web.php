<?php

Route::group(['namespace' => "TunnelConflux\DevCrud\Http\Controllers", 'middleware' => ['web']], function () {
    Route::get('contact', 'TestController@index');
    Route::post('contact', 'TestController@store')->name('contact');
});