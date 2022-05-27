<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function () {
    // 短信验证码
    Route::post('verificationCodes', 'VerificationCodesController@store')
        ->name('verificationCodes.store');
    // 用户注册
    Route::post('users', 'UsersController@store')
        ->name('users.store');
});
