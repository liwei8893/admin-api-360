<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Users\Mapper\UsersMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\MineModel;

class UsersAppService extends AbstractService
{
    /**
     * @var UsersMapper
     */
    #[Inject]
    public $mapper;

    public function updateInfo($params): ?MineModel
    {
        $userId = user('app')->getId();
        $state = $this->mapper->update($userId, $params);
        if (! $state) {
            throw new NormalStatusException('信息更新失败!');
        }
        return $this->mapper->read($userId);
    }
}
