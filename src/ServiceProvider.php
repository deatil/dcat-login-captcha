<?php

namespace Lake\LoginCaptcha;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasFormResponse;
use Dcat\Admin\Extend\ServiceProvider as BaseServiceProvider;

use Lake\LoginCaptcha\Captcha\Captcha;

/**
 * 服务提供者
 *
 * @create 2021-2-28
 * @author deatil
 */
class ServiceProvider extends BaseServiceProvider
{
    use HasFormResponse;
    
    /**
     * 路由过滤
     */
    protected $exceptRoutes = [
        'auth' => 'lake-login/captcha',
        'permission' => 'lake-login/captcha',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // 设置别名
        if (! class_exists('LakeLoginCaptcha')) {
            class_alias(__CLASS__, 'LakeLoginCaptcha');
        }
        
        Admin::booting(function () {
            $except = admin_base_path('auth/login');
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            
            // 匹配登陆get
            if (Helper::matchRequestPath('get:'.$except)) {
                $defaultCaptcha = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAAAyCAYAAACQyQOIAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAASrSURBVHhe7VuxcqMwEN2kM8zNFXAtbUxp13w+tV3GaamhuLkBd3cnOYmDVgtaBGgUI2bcBEn79u2TtFqRp3/igfBYM/D3z+/Rvs8/flqP7bLjUxCCS7r9tfXsL7SAzCUDQQgu2fbYVhCCx8FxCS0IwSXbHtsKQvA4OC6hBSG4ZNtjW0EIHgfHJbQgBJdse2wrCMHj4LiEFoTgkm2PbQUheBwcl9DCXcMqbDdwKV+h7o+d5lDsk1WsLTFoWBGWYPEBxghCeIAgznVBXqVvWAgdVOcSynMF3Vwmv3H/z+8pNiaEj+CXQgDlCar2G0dwYegbE8LC7D3QcEEIDxRMG1fkp3S3n03n0OfxGJhcR+iqM5wGNtc4O8Ihi3SWugrOpwqoLXmwz22UsfM48U70SPMCtON6c4HyVTnVD0aS7P/Rmu/7QrgxSmsezcJlC6G5lGDkMs7geMjgUwpjxKnQYsiOB9A1RBOaw+s4FoQDZgphuu807mNUDU6iGx8DRaf5PJqFwNga3jNtowjMtkZatFCdLmL+M57aIAI5RCsIv7BGMxhc0HeBe2glvYMQbebBnsAj8ty4ItBqTCEv9qAUTOWy9QbwQq4IxIwnljl9m6CX//fJ09sCyCWTWmVkYNGxEa8ePYLsfR/CrWIixyfwfLWz5dE8wcZXBEHwG84HJFAsAmknyuDQE8G76Z1Y8gsoCmLZl+3zVEHYNjWruKPt48RYYlmApp5RKprtu05+mqs8RNkLZDFq13Zw1bquw2PfzKgQurpBCZ5Q5MtXDmDSWZTtiX3/q1fXIpdJEpAVIcSMurtJxN8RqW2nU2rC/Pl+ru+aHRJ3BGmClXAFQYPyrMIjAjgqhGuH8vw4gZQ4FJjIlclWeavmqT/jnkkMHCfpPRlFdEG8Qx2uLWuFofAv5buJmynvl+QR2x0RQgd4wsIuHggC7Y7c22Tw1000p1DJbTvfd64lTjsXPDJODRyoRBtxZLOZ8ZbWHrebIx6nCYG91IrsvMIFHJnxysSx90PJ4rxoErN43oBqb7bvSxp1x+OIECJ9z20b4CXiV8DpBc6YbekaTAC7GhotpRnKJ0zW5/huGnvK+/V4nJAjACS/1OOdPJJV1RKFGgmDUjuDpLqCijgV6lm+OLzGFpntB4R1fWf4yW5iySMaf3xrII5kIKpfZUlUAWUZ9/6Rxw4idCqqK/UDkOZi+z2AXj0bKszox0xqpgthUdq29p0dQUbDNXlUzRsri+TFz5ALvaoYvz7eHwxXLIcri+MsDt1diF6Gewe1WDXBvlIRJPoNVDB1nhjVR6OEiMqvoQ8jWUxgX+SANwkTFrJq1u8kibFJFuMYcAlGxTIiAtkw2QPfrJ3vJm6mvF+Nx0lbw72xJERm+2OCECpUSswRZIcCjloNFUDeKRRaOZpJz06UpgUWMpjy9o4qZ6Ohk73wha0GG9+ZvrCarcQjss3YGlhoV2r0/f4/YCUiVh+WsTWsjiEY8ICBIAQPguADhCAEH6LgAYYgBA+C4AOEIAQfouABhiAED4LgAwTPj48+ULQNDGFF2EacjV4GIRgp2kaDIIRtxNnoZRCCkaJtNAhC2EacjV7+B1T7RDhNUhVIAAAAAElFTkSuQmCC';
                $captchaUrl = admin_route('lake-login-captcha.show');
                $script = '
                ;(function() {
                    var captcha_tpl = \'<fieldset class="form-label-group form-group position-relative has-icon-left lake-login-captcha">\'
                        captcha_tpl += \'<input id="captcha" type="text" style="width:70%;" class="form-control" name="captcha" placeholder="'.static::trans('captcha.enter_captcha').'" required>\'
                        captcha_tpl += \'<span class="captcha-img" style="width:28%;position: absolute;top: 0;right: 0;border-radius: .25rem;border: 1px solid #dbe3e6;">\'
                        captcha_tpl += \'<img id="verify" src="'.$defaultCaptcha.'" data-src="'.$captchaUrl.'" alt="'.static::trans('captcha.captcha').'" title="'.static::trans('captcha.refresh_captcha').'" class="captcha" style="cursor: pointer;width: 100%;border-radius: .25rem;">\'
                        captcha_tpl += \'</span>\'
                        captcha_tpl += \'<div class="form-control-position">\'
                        captcha_tpl += \'<i class="feather icon-image"></i>\'
                        captcha_tpl += \'</div>\'
                        captcha_tpl += \'<label for="captcha">'.static::trans('captcha.captcha').'</label>\'
                        captcha_tpl += \'<div class="help-block with-errors"></div>\'
                        captcha_tpl += \'</fieldset>\';
                    $(captcha_tpl).insertAfter($("#login-form fieldset.form-label-group").get(1));
                    $("#verify").click(function() {
                        var verifyimg = $(this).data("src");
                        $(this).attr("src", verifyimg.replace(/\?.*$/, "") + "?" + Math.random());
                    });
                    $(".lake-login-captcha .with-errors").bind("DOMNodeInserted", function() {
                        if ($("#captcha").val() != "" && $(this).html().length > 0) {
                            $("#verify").trigger("click");
                        }
                    });
                    setTimeout(function() {
                        $("#verify").trigger("click");
                    }, 50);
                })();
                ';
                Admin::script($script);
            }
            
            // 匹配登陆post
            if (Helper::matchRequestPath('post:'.$except)) {
                $captcha = request()->input('captcha');
                
                $validator = Validator::make([
                    'captcha' => $captcha,
                ], [
                    'captcha' => 'required',
                ]);
                
                if ($validator->fails()) {
                    $this->returnError($validator);
                }
                
                if (! (new Captcha())->check($captcha)) {
                    $this->returnError([
                        'captcha' => static::trans('captcha.captcha_error')
                    ]);
                }
            }
        });
        
    }
    
    /**
     * 返回错误信息
     */
    protected function returnError($msg)
    {
        $response = $this->validationErrorsResponse($msg);
        throw new HttpResponseException($response);
    }
    
    /**
     * 设置
     */
    public function settingForm()
    {
        return new Setting($this);
    }
    
}