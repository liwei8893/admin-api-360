<?php

namespace App\Users\Service;

use App\Users\Mapper\UserSalePlatformMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;

class UserSalePlatformService extends AbstractService
{
    /**
     * @var UserSalePlatformMapper
     */
    #[Inject]
    public $mapper;

    /**
     * 获取平台编号,挂载到数组
     * @param array $data
     * @return array
     * author:ZQ
     * time:2022-08-17 11:02
     */
    public function withPlatformNum(array $data): array
    {
        // 是否有平台,有平台就生成编号
        if (!empty($data['platform'])){
            $platformData = $this->mapper->getPlatformNum($data['platform']);
            if (!empty($platformData['sale_platform'])){
                $data['sale_platform'] = $platformData['sale_platform'];
                $data['old_platform'] = $platformData['old_platform'];
            }
        }
        return $data;
    }
}