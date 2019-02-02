<?php


Route::group(['namespace'=>'Devmi\EasySocialite\Http\Controllers', 'middleware' => ['web', 'easysocialite.activated']], function () {
    Route::get('/login/{provider}', 'Auth\SocialLoginController@redirectToProvider');
    Route::get('/login/{provider}/callback', 'Auth\SocialLoginController@handleProviderCallback');
});