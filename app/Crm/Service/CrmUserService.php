<?php

namespace App\Crm\Service;

use App\Crm\Mapper\CrmUserMapper;
use App\System\Model\SystemUser;
use App\Users\Model\User;
use App\Users\Model\UsersDetail;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

class CrmUserService extends AbstractService
{

    /**
     * @var CrmUserMapper
     */
    #[Inject]
    public $mapper;

    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        $params = $this->handleData($params);
        return parent::getPageList($params, $isScope);
    }

    /**
     * 用户详情
     * @param int $id
     * @return array
     */
    public function detail(int $id): array
    {
        $user = $this->mapper->read($id);
        if (!$user) {
            throw new NormalStatusException('用户不存在');
        }
        $user->load(['userDetail']);
        return $user->toArray();
    }

    /**
     * 批量分配用户给老师.
     * @param array $params
     * @return bool
     */
    public function batchDistro(array $params): bool
    {
        $userIds = (array)$params['userIds'];
        $adminId = (int)$params['adminId'];
        // 如果 adminId 是0,表示取消分配
        if ($adminId === 0) {
            return User::query()->whereIn('id', $userIds)->update(['created_by' => 0, 'created_name' => '']);
        }
        // 查询老师信息,获取老师用户名
        $teacherInfo = SystemUser::query()->find($adminId);
        if (!$teacherInfo) {
            throw new NormalStatusException('老师不存在');
        }
        // 更新用户信息,指定老师 Id, 指定老师用户名
        $updateData = [
            'created_by' => $teacherInfo->id,
            'created_name' => $teacherInfo->nickname,
        ];
        return User::query()->whereIn('id', $userIds)->update($updateData);
    }

    /**
     * 获取系统用户列表
     * @param array $params
     * @return array
     */
    public function systemUserIndex(array $params): array
    {
        return $this->mapper->systemUserIndex($params);
    }

    /**
     * 保存用户详情.
     * @param array $params
     * @return array
     */
    public function saveDetail(array $params): array
    {
        return UsersDetail::query()->updateOrCreate(['id' => $params['id']], $params)->toArray();
    }

    /**
     * 处理提交数据.
     */
    protected function handleData(array $params): array
    {
        if (!isset($params['orderBy'])) {
            $params['orderBy'] = ['id'];
        }
        if (!isset($params['orderType'])) {
            $params['orderType'] = ['desc'];
        }
        return $params;
    }
}
