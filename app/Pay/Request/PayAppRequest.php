<?php

declare(strict_types=1);

namespace App\Pay\Request;

use Mine\MineFormRequest;

class PayAppRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function OAuthsRules(): array
    {
        return ['code' => 'required', 'authId' => 'required'];
    }

    public function wxAuthRules(): array
    {
        return ['authId' => 'required', 'redirectUrl' => 'required'];
    }
}
