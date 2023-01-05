<?php

declare(strict_types=1);

namespace App\Question\Service;

use _PHPStan_80b5cdd3e\Nette\DI\Attributes\Inject;
use App\Question\Mapper\DenseVolumeMapper;
use Mine\Abstracts\AbstractService;
use Mine\MineModel;

/**
 * 黄冈密卷服务类.
 */
class DenseVolumeService extends AbstractService
{
    /**
     * @var DenseVolumeMapper
     */
    #[Inject]
    public $mapper;

    public function getUrl(array $params): MineModel
    {
        return $this->mapper->first(['id' => $params['id']], ['url', 'subject']);
    }
}
