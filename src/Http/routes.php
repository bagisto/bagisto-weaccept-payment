<?php

Route::group(['middleware' => ['web']], function () {
    Route::prefix('/weaccept')->group(function () {

        Route::get('/redirect', 'Webkul\WeAccept\Http\Controllers\WeAcceptController@redirect')->name('weaccept.payement.redirect');

        Route::get('/paymob_notification_callback', 'Webkul\WeAccept\Http\Controllers\WeAcceptController@success')->name('weaccept.payement.success');

        Route::get('/paymob_txn_response_callback', 'Webkul\WeAccept\Http\Controllers\WeAcceptController@callback')->name('weaccept.payement.cancel');
    });
});