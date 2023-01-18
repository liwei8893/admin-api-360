<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\DenseVolumeMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\SubjectAuth;
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

    #[SubjectAuth]
    public function getUrl(int $id): MineModel
    {
        return $this->mapper->first(['id' => $id], ['url', 'subject', 'grade']);
    }
}
