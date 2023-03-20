<?php

namespace Lake\LoginCaptcha;

use Dcat\Admin\Extend\Setting as Form;
use Dcat\Admin\Support\Helper;

/**
 * 设置
 *
 * @create 2021-4-5
 * @author deatil
 */
class Setting extends Form
{
    /**
     * 设置标题
     */
    public function title()
    {
        return $this->trans('captcha.setting');
    }

    /**
     * 格式化
     */
    protected function formatInput(array $input)
    {
        $input['captcha_type'] = $input['captcha_type'] ?: 'string';
        $input['fontsize'] = $input['fontsize'] ?: 20;
        $input['codelen'] = $input['codelen'] ?: 4;
        $input['charset'] = $input['charset'] ?: 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
        $input['captcha_position'] = $input['captcha_position'] ?? '';

        return $input;
    }
    
    /**
     * 设置表单
     */
    public function form()
    {
        $this->radio('captcha_type', $this->trans('captcha.captcha_type'))
            ->options([
                'string' => $this->trans('captcha.captcha_type_string'),
                'math' => $this->trans('captcha.captcha_type_math'),
            ])
            ->default('string');
        $this->text('fontsize', $this->trans('captcha.fontsize'))
            ->default('20');
        $this->text('codelen', $this->trans('captcha.codelen'))
            ->default('4')
            ->help($this->trans('captcha.codelen_help'));
        $this->textarea('charset', $this->trans('captcha.charset'))
            ->default('abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789')
            ->help($this->trans('captcha.charset_help'));
        $this->text('captcha_position', $this->trans('captcha.captcha_position'))
            ->default('')
            ->help($this->trans('captcha.captcha_position_help'));
    }
}
