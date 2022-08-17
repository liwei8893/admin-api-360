<?php

namespace App\Users\Service;

use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class UserSalePlatformService extends AbstractService
{
    /**
     * @var UserSalePlatformMapper
     */
    #[Inject]
    public $mapper;
}