<?php

namespace Lake\LoginCaptcha\Http\Controllers;

use Lake\LoginCaptcha\Captcha\Captcha as CaptchaImg;

/**
 * 验证码
 *
 * @create 2021-2-28
 * @author deatil
 */
class Captcha
{
    /**
     * 展示验证码
     */
    public function show()
    {
        (new CaptchaImg())->makeCode()->showImage();
    }
}
