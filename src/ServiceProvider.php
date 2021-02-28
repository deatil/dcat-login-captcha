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
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // 加载路由
        $this->app->booted(function () {
            $this->registerRoutes(__DIR__.'/../routes/admin.php');
            
            // 路由过滤
            $exceptRoutes = ['lake-login/captcha'];
            Admin::context()->addMany('auth.except', $exceptRoutes);
            Admin::context()->addMany('permission.except', $exceptRoutes);
        });
        
        Admin::booting(function () {
            $except = admin_base_path('auth/login');

            if ($except !== '/') {
                $except = trim($except, '/');
            }
            
            // 匹配登陆get
            if (Helper::matchRequestPath('get:'.$except)) {
                $script = '
                    var captcha_tpl = \'\
                    <fieldset class="form-label-group form-group position-relative has-icon-left login-captcha">\
                        <input id="captcha" type="text" style="width:70%;" class="form-control" name="captcha" placeholder="请输入验证码" required>\
                        <span class="captcha-img" style="width:28%;position: absolute;top: 0;right: 0;">\
                            <img id="verify" src="'.admin_route('lake-login-captcha.show').'" alt="验证码" title="点击刷新验证码" class="captcha" style="cursor: pointer;width: 100%;border-radius: .25rem;border-color: #dbe3e6;">\
                        </span>\
                        <div class="form-control-position">\
                            <i class="feather icon-image"></i>\
                        </div>\
                        <label for="captcha">验证码</label>\
                        <div class="help-block with-errors"></div>\
                    </fieldset>\
                    \';
                    $(captcha_tpl).insertAfter($("#login-form fieldset.form-label-group").get(1));
                    $("#verify").click(function() {
                        var verifyimg = $("#verify").attr("src");
                        $("#verify").attr("src", verifyimg.replace(/\?.*$/, "") + "?" + Math.random());
                    });
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
                        'captcha' => '验证码错误'
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
    
}