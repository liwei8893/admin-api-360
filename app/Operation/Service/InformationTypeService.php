<?php
declare(strict_types=1);


namespace App\Operation\Service;

use App\Operation\Mapper\InformationTypeMapper;
use Mine\Abstracts\AbstractService;

/**
 * 资讯分类服务类
 */
class InformationTypeService extends AbstractService
{
    /**
     * @var InformationTypeMapper
     */
    public $mapper;

    public function __construct(InformationTypeMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
