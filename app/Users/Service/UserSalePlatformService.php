<?php

namespace App\Users\Service;

use App\Users\Mapper\UserSalePlatformMapper;
use Hyperf\Di\Annotation\Inject;
use JetBrains\PhpStorm\ArrayShape;
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

    /**
     * 获取平台编号
     * @param string $platform
     * @return array
     * author:ZQ
     * time:2022-08-28 13:41
     */
    #[ArrayShape(['platform'=>'string','sale_platform'=>'string','old_platform'=>'string'])]
    public function getPlatformNum(string $platform): array
    {
        return $this->mapper->getPlatformNum($platform);
    }
}