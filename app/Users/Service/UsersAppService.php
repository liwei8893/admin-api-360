<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Score\Service\AvatarService;
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

    #[Inject]
    protected AvatarService $avatarService;

    public function updateInfo($params): ?MineModel
    {
        $userId = user('app')->getId();
        $state = $this->mapper->update($userId, $params);
        if (! $state) {
            throw new NormalStatusException('信息更新失败!');
        }
        return $this->mapper->read($userId);
    }

    public function setAvatar($params): ?MineModel
    {
        $avatarModel = $this->avatarService->read($params['id']);
        if (! $avatarModel) {
            throw new NormalStatusException('头像不存在!');
        }
        $userModel = $this->read(user('app')->getId());
        if (! $userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        if ($avatarModel->type === 1) {
            $userModel->avatar = $avatarModel->url;
        } elseif ($avatarModel->type === 2) {
            $userModel->avatar_frame = $avatarModel->url;
        } else {
            throw new NormalStatusException('头像类型错误!');
        }
        if (! $userModel->save()) {
            throw new NormalStatusException('图片更新失败,请稍后重试!');
        }
        return $userModel->fresh();
    }

    /*
     * 获取用户已拥有头像
     * @param $params
     * @return array
     */
    public function getAvatar($params): array
    {
        $userModel = $this->read(user('app')->getId());
        if (! $userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        $avatarModel = $userModel->avatarTable();
        if (isset($params['type'])) {
            $avatarModel->where('type', $params['type']);
        }
        $pageSize = $params['pageSize'] ?? '15';
        return $this->mapper->setPaginate($avatarModel->paginate((int) $pageSize));
    }
}
