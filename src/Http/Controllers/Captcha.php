<?php

namespace Lake\LoginCaptcha\Http\Controllers;

use Lake\LoginCaptcha\Captcha\Captcha as CaptchaImg;
use Lake\LoginCaptcha\ServiceProvider as LakeLoginCaptcha;

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
        $charset = LakeLoginCaptcha::setting('charset');
        $codelen = LakeLoginCaptcha::setting('codelen');
        $fontsize = LakeLoginCaptcha::setting('fontsize');
        
        (new CaptchaImg())
            ->withConfig([
                'charset' => $charset ?: 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789',
                'codelen' => $codelen ?: 4,
                'fontsize' => $fontsize ?: 20,
            ])
            ->makeCode()
            ->showImage();
    }
}
