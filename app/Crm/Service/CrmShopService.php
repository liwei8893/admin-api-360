<?php
declare(strict_types=1);


namespace App\Crm\Service;

use App\Crm\Mapper\CrmShopMapper;
use Mine\Abstracts\AbstractService;

/**
 * 商品管理服务类
 */
class CrmShopService extends AbstractService
{
    /**
     * @var CrmShopMapper
     */
    public $mapper;

    public function __construct(CrmShopMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
