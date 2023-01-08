<?php

declare(strict_types=1);

namespace App\Users\Model;

use Mine\MineModel;

/**
 * @property int $id
 * @property string $user_platform 平台
 * @property int $u_sale_platform
 */
class UserSalePlatform extends MineModel
{
    public bool $timestamps = false;

    protected ?string $table = 'user_sale_platform';

    protected array $fillable = ['user_platform', 'u_sale_platform'];
}
