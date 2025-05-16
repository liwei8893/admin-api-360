<?php
declare(strict_types=1);


namespace App\Crm\Service;

use App\Crm\Mapper\CrmUserAddressMapper;
use Mine\Abstracts\AbstractService;

/**
 * 用户地址信息服务类
 */
class CrmUserAddressService extends AbstractService
{
    /**
     * @var CrmUserAddressMapper
     */
    public $mapper;

    public function __construct(CrmUserAddressMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
