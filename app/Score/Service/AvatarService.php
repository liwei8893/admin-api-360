<?php

declare(strict_types=1);

namespace App\Score\Service;

use App\Score\Mapper\AvatarMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class AvatarService extends AbstractService
{
    /**
     * @var AvatarMapper
     */
    #[Inject]
    public $mapper;
}
