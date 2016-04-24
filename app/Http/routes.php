<?php
/*
 * The underlying class for Route facade is \Illuminate\Contracts\Routing\Router
 */
Route::group([ 'namespace' => 'Api', 'prefix' => 'api' ], function () {
    //==========================================
    //=====                 User           =====
    //==========================================
    Route::group([ 'prefix' => 'users' ], function () {
        Route::post('/', 'Users\UserController@register');
    });

    Route::group([ 'prefix' => 'vcodes' ], function () {
        Route::post('/', 'Vcodes\VcodeController@sendVcode');
    });

});