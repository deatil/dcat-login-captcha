<?php

use Lake\LoginCaptcha\Http\Controllers\Captcha;

// 验证码
Route::get('lake-login/captcha', Captcha::class.'@show')->name('lake-login-captcha.show');
