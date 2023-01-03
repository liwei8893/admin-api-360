<?php

declare(strict_types=1);

namespace App\Question\Service;

use App\Question\Mapper\DenseVolumeMapper;
use Mine\Abstracts\AbstractService;

/**
 * 黄冈密卷服务类.
 */
class DenseVolumeService extends AbstractService
{
    /**
     * @var DenseVolumeMapper
     */
    public $mapper;

    public function __construct(DenseVolumeMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getUrl($params)
    {
        return $this->mapper->first(['id' => $params['id']], ['url']);
    }
}
