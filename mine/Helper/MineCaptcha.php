<?php


declare(strict_types=1);

namespace Mine\Helper;

use EasySwoole\VerifyCode\Config;
use EasySwoole\VerifyCode\VerifyCode;

class MineCaptcha
{
    public function getCaptchaInfo(): array
    {
        $conf = new Config();
        $conf->setUseCurve()->setUseNoise();
        $validCode = new VerifyCode($conf);
        $draw = $validCode->DrawCode();
        return ['code' => Str::lower($draw->getImageCode()), 'image' => $draw->getImageByte()];
    }
}
