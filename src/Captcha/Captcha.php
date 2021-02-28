<?php

namespace Lake\LoginCaptcha\Captcha;

use Illuminate\Support\Facades\Session;

/**
 * 图形验证码
 *
 * @create 2021-2-28
 * @author deatil
 */
class Captcha
{
    // 验证码
    private $code = ''; 
    
    // 唯一序号
    private $uniqid = ''; 
    
    // 随机因子
    private $charset = 'ABCDEFGHKMNPRSTUVWXYZ23456789'; 
    
    // 验证码长度
    private $codelen = 4; 
    
    // 宽度
    private $width = 130; 
    
    // 高度
    private $height = 50; 
    
    // 图形资源句柄
    private $img = ''; 
    
    // 指定的字体
    private $font = ''; 
    
    // 指定字体大小
    private $fontsize = 20; 
    
    // 指定字体颜色
    private $fontcolor = ''; 
    
    /**
     * 设置配置
     * 
     * @param string|array $name
     * @return string $value
     *
     * @return object
     */
    public function withConfig($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->withConfig($k, $v);
            }
            
            return $this;
        }
        
        if (isset($this->{$name})) {
            $this->{$name} = $value;
        }
        
        return $this;
    }

    /**
     * 生成验证码信息
     *
     * @return object
     */
    public function makeCode()
    {
        // 生成验证码序号
        if (empty($this->uniqid)) {
            $this->uniqid = 'LoginCaptcha';
        }
        
        // 生成验证码字符串
        $length = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $length)];
        }
        
        // 缓存验证码字符串
        Session::put($this->uniqid, $this->code);
        
        // 设置字体文件路径
        $this->font = __DIR__ . '/font/icon.ttf';
        
        return $this;
    }

    /**
     * 显示验证码图片
     * @return string
     */
    public function showImage()
    {
        // 生成背景
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(220, 255), mt_rand(220, 255), mt_rand(220, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
        
        // 生成线条
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 50), mt_rand(0, 50), mt_rand(0, 50));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        
        // 生成雪花
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
        
        // 生成文字
        $_x = $this->width / $this->codelen;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), intval($_x * $i + mt_rand(1, 5)), intval($this->height / 1.4), $this->fontcolor, $this->font, $this->code[$i]);
        }
        
        ob_clean();
        imagepng($this->img);
    }
    
    /**
     * 检查验证码是否正确
     * @param string $code 需要验证的值
     * @param string $uniqid 验证码编号
     * @return boolean
     */
    public function check($code, $uniqid = 'LoginCaptcha')
    {
        if (empty($uniqid)) {
            return false;
        }
        $val = Session::pull($uniqid); // 获取并删除
        return is_string($val) && strtolower($val) === strtolower($code);
    }
}
