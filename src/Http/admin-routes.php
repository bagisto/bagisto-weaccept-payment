<?php
Route::group(['middleware' => ['web']], function () {
    
    Route::prefix('sales')->group(function () {
        
        Route::group(['middleware' => ['admin']], function () {
            
            Route::get('/weaccept/refunds/create/{order_id}', 'Webkul\WeAccept\Http\Controllers\WeAcceptController@createRefund')->defaults('_config', [
                'view' => 'weaccept::admin.sales.refunds.create',
            ])->name('admin.sales.weaccept.refunds.create');

            Route::post('/weaccept/refunds/create/{order_id}', 'Webkul\WeAccept\Http\Controllers\WeAcceptController@storeRefund')->defaults('_config', [
                'redirect' => 'admin.sales.orders.view',
            ])->name('admin.sales.weaccept.refunds.store');

        });
    });
});
?>


