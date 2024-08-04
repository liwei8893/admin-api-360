<?php
declare(strict_types=1);

namespace App\Play\Service;

use App\Play\Mapper\PlayIdiomMapper;
use Mine\Abstracts\AbstractService;

/**
 * 成语接龙服务类
 */
class PlayIdiomService extends AbstractService
{
    /**
     * @var PlayIdiomMapper
     */
    public $mapper;

    public function __construct(PlayIdiomMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMaxId(): int
    {
        $level = $this->mapper->getMaxId();
        return $level ?? 0;
    }
}
