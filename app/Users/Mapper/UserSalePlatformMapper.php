<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Users\Mapper;

use App\Users\Model\UserSalePlatform;
use JetBrains\PhpStorm\ArrayShape;
use Mine\Abstracts\AbstractMapper;
use Mine\Exception\NormalStatusException;

class UserSalePlatformMapper extends AbstractMapper
{
    public function assignModel(): void
    {
        $this->model = UserSalePlatform::class;
    }

    /**
     * 获取平台编号.
     * @return array
     *               author:ZQ
     *               time:2022-08-28 13:47
     */
    #[ArrayShape(['platform' => 'string', 'sale_platform' => 'string', 'old_platform' => 'string'])]
    public function getPlatformNum(string $platform): array
    {
        if (! $platform) {
            throw new NormalStatusException('缺少平台编号');
        }
        // 用新平台编号表找到最小的可用编号
        $minPlatform = UserSalePlatform::query()->where('user_platform', $platform)
            ->orderBy('u_sale_platform')->first();
        // 如果没有最小可用编号表示没有生成这个平台编号
        if (! $minPlatform) {
            throw new NormalStatusException('生成平台编号失败！');
        }
        $platformData['platform'] = $platform;
        $platformData['sale_platform'] = $minPlatform['u_sale_platform'];
        $platformData['old_platform'] = $platform . $minPlatform['u_sale_platform'];
        UserSalePlatform::query()->where('id', $minPlatform['id'])->delete();
        return $platformData;
    }
}
