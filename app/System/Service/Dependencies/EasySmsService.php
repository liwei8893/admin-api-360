<?php

declare(strict_types=1);

namespace App\System\Service\Dependencies;

use Overtrue\EasySms\EasySms;

class EasySmsService extends EasySms
{
    public function __construct()
    {
        parent::__construct(config('easysms'));
    }
}
