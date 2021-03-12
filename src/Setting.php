<?php

namespace Lake\LoginCaptcha;

use Dcat\Admin\Extend\Setting as Form;
use Dcat\Admin\Support\Helper;

class Setting extends Form
{
    public function title()
    {
        return $this->trans('captcha.setting');
    }

    protected function formatInput(array $input)
    {
        $input['charset'] = $input['charset'] ?: 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
        $input['codelen'] = $input['codelen'] ?: 4;
        $input['fontsize'] = $input['fontsize'] ?: 20;

        return $input;
    }
    
    public function form()
    {
        $this->textarea('charset', ServiceProvider::trans('captcha.charset'))
            ->default('abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789');
        $this->text('codelen', ServiceProvider::trans('captcha.codelen'))
            ->default('4');
        $this->text('fontsize', ServiceProvider::trans('captcha.fontsize'))
            ->default('20');
    }
}
