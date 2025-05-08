<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Crm\Service\CrmUserTimelineService;
use App\System\Service\SystemDeptService;
use App\System\Service\SystemDictDataService;
use App\Users\Mapper\UsersMapper;
use App\Users\Model\User;
use Closure;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Stringable\Str;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Resubmit;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Mine\Helper\LoginUser;
use Mine\Redis\MineLockRedis;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use function Hyperf\Collection\collect;
use function Hyperf\Config\config;

/**
 * 用户表服务类.
 */
class UsersService extends AbstractService
{
    /**
     * @var UsersMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    protected SystemDictDataService $systemDictDataService;

    #[Inject]
    protected SystemDeptService $systemDeptService;

    #[Inject]
    protected LoginUser $loginUser;

    #[Inject]
    protected UserSalePlatformService $userSalePlatformService;

    #[Inject]
    protected CrmUserTimelineService $crmUserTimelineService;

    /**
     * 更换手机号.
     */
    #[Transaction]
    public function changeMobile(array $params): bool
    {
        /* @var User $userModel */
        $userModel = $this->mapper->read($params['userId']);
        if (!$userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        $oldMobile = $userModel->mobile;
        $newMobile = $params['mobile'];
        /* @var User $newUserModel */
        $newUserModel = $this->mapper->readByMobile((string)$newMobile);
        // 新手机号不为空，交换手机号
        if ($newUserModel) {
            // 新手机号先设置为空
            $newUserModel->mobile = '';
            if (!$newUserModel->save()) {
                throw new NormalStatusException('删除新手机号时失败!');
            }
            // 老手机号设置为新手机号
            $userModel->mobile = $newMobile;
            $userModel->user_pass = $this->mapper->getInitPassword((string)$newMobile);
            if (!$userModel->save()) {
                throw new NormalStatusException('修改手机号时失败!');
            }
            // 新手机号设置为老手机号
            $newUserModel->mobile = $oldMobile;
            $newUserModel->user_pass = $this->mapper->getInitPassword((string)$oldMobile);
            if (!$newUserModel->save()) {
                throw new NormalStatusException('修改手机号时失败!');
            }
            return true;
        }
        // 新手机号为空，更换手机号
        $userModel->mobile = $newMobile;
        $userModel->user_pass = $this->mapper->getInitPassword((string)$newMobile);
        if (!$userModel->save()) {
            throw new NormalStatusException('修改手机号时失败!');
        }
        return true;
    }

    /**
     * 用手机号查询一条数据.
     * @param mixed $mobile
     */
    public function readByMobile(string $mobile): Model|Builder
    {
        $model = $this->mapper->readByMobile($mobile);
        if (!$model) {
            throw new NormalStatusException('此手机号用户不存在!');
        }
        return $model;
    }

    /**
     * 创建用户.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function save(array $data): int
    {
        $lockRedis = new MineLockRedis();
        $lockRedis->setTypeName('user:save:lock');
        $lockKey = $data['mobile'];
        try {
            $lockRedis->lock($lockKey, 2);
            if ($this->existsByMobile((string)$data['mobile'])) {
                throw new NormalStatusException('手机号已存在');
            }
            $data = $this->handleSaveData($data);
            $userId = $this->mapper->save($data);
            // 保存crm用户时间线
            if ($userId) {
                $this->crmUserTimelineService->saveCreatedUserEvent($userId, $this->loginUser->getId(), "管理员[{$this->loginUser->getNickname()}]注册账号");
            }
            return $userId;
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new NormalStatusException($e->getMessage(), 500);
        } finally {
            $lockRedis->freed($lockKey);
        }
    }

    /**
     * 用手机号检测用户是否存在.
     */
    public function existsByMobile(string $mobile): bool
    {
        return $this->mapper->existsByMobile($mobile);
    }

    public function handleSaveData(array $data): array
    {
        // 保存时删除创建时间
        if (isset($data['created_at'])) {
            unset($data['created_at']);
        }
        // 获取平台编号,挂载到数组
        $data = $this->userSalePlatformService->withPlatformNum($data);
        // 合并初始化参数
        return array_merge([
            'mobile' => $data['mobile'],
            'user_name' => $this->getInitUserName((string)$data['mobile']),
            'user_nickname' => $this->getInitUserName((string)$data['mobile']),
            'real_name' => $this->getInitUserName((string)$data['mobile']),
            'user_pass' => $this->getInitPassword((string)$data['mobile']),
            'avatar' => config('hxt-app.defaultAvatar'),
            'user_type' => 1,
            'status' => 1,
            'sex' => 3,
            'created_id' => $this->loginUser->getId(),
            'created_name' => $this->loginUser->getNickname(),
        ], $data);
    }

    /**
     * 获取初始用户名.
     */
    public function getInitUserName(string $mobile): string
    {
        return $this->mapper->getInitUserName($mobile);
    }

    /**
     * 获取初始密码
     */
    public function getInitPassword(string $mobile): string
    {
        return $this->mapper->getInitPassword($mobile);
    }

    /**
     * 批量更换平台.
     */
    public function batchChangePlatform(array $params): array
    {
        $logInfo = [];
        $mobilesArr = array_unique($params['mobiles']);
        foreach ($mobilesArr as $mobile) {
            // 查询用户
            /* @var User $userModel */
            $userModel = $this->mapper->readByMobile((string)$mobile);
            if (!$userModel) {
                $logInfo[] = ['mobile' => $mobile, 'info' => '未查询到用户'];
                continue;
            }
            // 查询平台是否一致
            if (!empty($userModel['platform']) && Str::upper($params['platform']) === Str::upper($userModel['platform'])) {
                $logInfo[] = ['mobile' => $mobile, 'info' => '平台一致不需要变更'];
                continue;
            }
            // 更换平台编号
            $status = $this->changePlatformByModel($userModel, $params['platform']);
            if (!$status) {
                $logInfo[] = ['mobile' => $mobile, 'info' => '失败'];
            }
            $logInfo[] = ['mobile' => $mobile, 'info' => '成功'];
        }
        return $logInfo;
    }

    /**
     * 更换平台.
     */
    public function changePlatformByModel(User $userModel, string $platform): bool
    {
        if (!empty($userModel->platform) && Str::upper($userModel->platform) === Str::upper($platform)) {
            return true;
        }
        $platformData = $this->userSalePlatformService->getPlatformNum($platform);
        $userModel->platform = $platformData['platform'];
        $userModel->sale_platform = $platformData['sale_platform'];
        $userModel->old_platform = $platformData['old_platform'];
        return $userModel->save();
    }

    /**
     * 更新用户信息.
     */
    public function update(int $id, array $data): bool
    {
        if (!empty($data['mobile'])) {
            unset($data['mobile']);
        }
        /* @var User $userModel */
        $userModel = $this->mapper->read($id);
        if (!$userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        // 更换平台
        if ($data['platform'] !== $userModel->platform) {
            $data = $this->userSalePlatformService->withPlatformNum($data);
        }
        return $this->mapper->update($id, $data);
    }

    /**
     * 初始化密码
     */
    public function initUserPassword(int $id, mixed $password = null): bool
    {
        return $this->mapper->initUserPassword($id, $password);
    }

    /**
     * 检测手机号是否存在.
     */
    public function hasMobile(string $mobile): bool
    {
        return $this->mapper->getModel()->query()->where('mobile', $mobile)->exists();
    }

    /**
     * 用户导入.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    #[Transaction, Resubmit(60)]
    public function import(string $dto, ?Closure $closure = null): bool
    {
        $grade = $this->systemDictDataService->getList(['code' => 'grade']);
        $platform = $this->systemDeptService->getPlatformSelect();
        $closure = $closure ?? function (User $model, $data) use ($grade, $platform) {
            $data = collect($data);
            $platform = collect($platform);
            $grade = collect($grade);
            $errMessage = [];
            // 数据验证
            foreach ($data as $key => $value) {
                $row = $key + 2;
                if (empty($value['user_name'])) {
                    $errMessage[] = "第{$row}行用户名不能为空";
                }
                if (empty($value['mobile']) || !preg_match('/^1[3456789]\\d{9}$/', $value['mobile'])) {
                    $errMessage[] = "第{$row}行手机号错误";
                }
                if (empty($value['platform']) || !$platform->contains('key', $value['platform'])) {
                    $errMessage[] = "第{$row}行平台错误";
                }
                if (empty($value['grade']) || !$grade->contains('title', $value['grade'])) {
                    $errMessage[] = "第{$row}行年级错误";
                }
            }
            if (!empty($errMessage)) {
                throw new NormalStatusException(implode(';', $errMessage));
            }
            // 所有要报名的手机号
            $mobiles = $data->pluck('mobile');
            // 系统已存在的用户
            $userModel = $model->whereIn('mobile', $mobiles)->get();
            /* @var User $user */
            foreach ($userModel as $user) {
                // 已存在的用户修改平台
                $userData = $data->where('mobile', $user->mobile)->first();
                if ($userData && !$user->platform) {
                    $this->changePlatformByModel($user, (string)$userData['platform']);
                }
                // 修改年级
                $gradeId = $grade->where('title', $userData['grade'])->first()['key'];
                if ($user->grade_id !== $gradeId) {
                    $user->grade_id = $gradeId;
                }
                // 修改用户名
                if ($user->user_name !== $userData['user_name']) {
                    $user->user_name = $userData['user_name'];
                }
                $user->save();
            }
            // 未报名的手机号
            $diffMobiles = $mobiles->diff($userModel->pluck('mobile'));
            $newCollection = $data->whereIn('mobile', $diffMobiles);
            foreach ($newCollection as $item) {
                $gradeId = $grade->where('title', $item['grade'])->first()['key'];
                $item['grade_id'] = $gradeId;
                $insertData = $this->handleSaveData($item);
                $userMod = $model->create($insertData);
                // 保存crm用户时间线
                if ($userMod) {
                    $this->crmUserTimelineService->saveCreatedUserEvent($userMod->id, $this->loginUser->getId(), "管理员[{$this->loginUser->getNickname()}]批量导入账号");
                }
            }
            return true;
        };
        return parent::import($dto, $closure);
    }

    public function getPlatformUser(array $params): array
    {
        if (!empty($params['mobile']) || !empty($params['user_name']) || !empty($params['old_platform'])) {
            $params['mobileEq'] = $params['mobile'] ?? '';
            $params['userNameEq'] = $params['user_name'] ?? '';
            $params['oldPlatformEq'] = $params['old_platform'] ?? '';
            unset($params['mobile'], $params['user_name'], $params['old_platform']);
            return $this->getPageList($params);
        }
        return $this->mapper->getNullPaginate();
    }

    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        $params = $this->handleData($params);
        return parent::getPageList($params, $isScope);
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

    /**
     * 需要处理导出数据时,重写函数.
     */
    protected function handleExportData(array &$data): void
    {
        if (!empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s', (int)$data['created_at']);
        }
    }
}
