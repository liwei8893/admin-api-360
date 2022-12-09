<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\System\Service\SystemDeptService;
use App\System\Service\SystemDictDataService;
use App\Users\Mapper\UsersMapper;
use App\Users\Model\Users;
use Closure;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Mine\Helper\LoginUser;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

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

    /**
     * 更换手机号.
     * @param mixed $params
     */
    public function changeMobile($params): bool
    {
        $newUserModel = $this->mapper->readByMobile($params['mobile']);
        if ($newUserModel) {
            throw new NormalStatusException('新手机号已存在,请手动操作!');
        }
        // 更换手机号
        $userModel = $this->mapper->read($params['userId']);
        if (! $userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        $userModel->mobile = $params['mobile'];
        $userModel->user_pass = $this->mapper->getInitPassword($params['mobile']);
        return $userModel->save();
    }

    /**
     * 批量更换平台.
     * @param mixed $params
     */
    public function batchChangePlatform($params): array
    {
        $logInfo = [];
        $mobilesArr = array_unique($params['mobiles']);
        foreach ($mobilesArr as $mobile) {
            // 查询用户
            $userModel = $this->mapper->readByMobile($mobile);
            if (! $userModel) {
                $logInfo[] = ['mobile' => $mobile, 'info' => '未查询到用户'];
                continue;
            }
            // 查询平台是否一致
            if (Str::upper($params['platform']) === Str::upper($userModel['platform'])) {
                $logInfo[] = ['mobile' => $mobile, 'info' => '平台一致不需要变更'];
                continue;
            }
            // 更换平台编号
            $platformData = $this->userSalePlatformService->getPlatformNum($params['platform']);
            $userModel->platform = $platformData['platform'];
            $userModel->sale_platform = $platformData['sale_platform'];
            $userModel->old_platform = $platformData['old_platform'];
            $status = $userModel->save();
            if (! $status) {
                $logInfo[] = ['mobile' => $mobile, 'info' => '失败'];
            }
            $logInfo[] = ['mobile' => $mobile, 'info' => '成功'];
        }
        return $logInfo;
    }

    /**
     * 创建用户.
     * @param mixed $data
     */
    public function save($data): int
    {
        if ($this->existsByMobile($data['mobile'])) {
            throw new NormalStatusException('手机号已存在');
        }
        $data = $this->handleSaveData($data);
        return $this->mapper->save($data);
    }

    /**
     * 用手机号检测用户是否存在.
     * @param mixed $mobile
     */
    public function existsByMobile($mobile): bool
    {
        return $this->mapper->existsByMobile($mobile);
    }

    /**
     * 更新用户信息.
     */
    public function update(int $id, array $data): bool
    {
        if (! empty($data['mobile'])) {
            unset($data['mobile']);
        }
        $userModel = $this->mapper->read($id);
        if (! $userModel) {
            throw new NormalStatusException('用户不存在!');
        }
        // 更换平台
        if ($data['platform'] !== $userModel->platform) {
            $data = $this->userSalePlatformService->withPlatformNum($data);
        }
        return $this->mapper->update($id, $data);
    }

    public function handleSaveData(array $data): array
    {
        // 获取平台编号,挂载到数组
        $data = $this->userSalePlatformService->withPlatformNum($data);
        // 合并初始化参数
        return array_merge([
            'mobile' => $data['mobile'],
            'user_name' => $this->getInitUserName($data['mobile']),
            'user_nickname' => $this->getInitUserName($data['mobile']),
            'real_name' => $this->getInitUserName($data['mobile']),
            'user_pass' => $this->getInitPassword($data['mobile']),
            'avatar' => config('hxt-app.defaultAvatar'),
            'user_type' => 1,
            'status' => 1,
            'sex' => 3,
            'created_id' => $this->loginUser->getId(),
            'created_name' => $this->loginUser->getUsername(),
        ], $data);
    }

    /**
     * 获取初始用户名.
     * @param mixed $mobile
     */
    public function getInitUserName($mobile): string
    {
        return $this->mapper->getInitUserName($mobile);
    }

    /**
     * 获取初始密码
     * @param mixed $mobile
     */
    public function getInitPassword($mobile): string
    {
        return $this->mapper->getInitPassword($mobile);
    }

    /**
     * 初始化密码
     */
    public function initUserPassword(int $id): bool
    {
        return $this->mapper->initUserPassword($id);
    }

    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        $params = $this->handleData($params);
        return parent::getPageList($params, $isScope);
    }

    /**
     * 用手机号查询一条数据.
     * @param mixed $mobile
     */
    public function readByMobile($mobile): Model|Builder
    {
        $model = $this->mapper->readByMobile($mobile);
        if (! $model) {
            throw new NormalStatusException('此手机号用户不存在!');
        }
        return $model;
    }

    /**
     * 用户导入.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    #[Transaction]
    public function import(string $dto, ?Closure $closure = null): bool
    {
        $grade = $this->systemDictDataService->getList(['code' => 'grade']);
        $platform = $this->systemDeptService->getPlatformSelect();
        $closure = $closure ?? function (Users $model, $data) use ($grade, $platform) {
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
                if (empty($value['mobile']) || ! preg_match('/^1[3456789]\\d{9}$/', $value['mobile'])) {
                    $errMessage[] = "第{$row}行手机号错误";
                }
                if (empty($value['platform']) || ! $platform->contains('key', $value['platform'])) {
                    $errMessage[] = "第{$row}行平台错误";
                }
                if (empty($value['grade']) || ! $grade->contains('title', $value['grade'])) {
                    $errMessage[] = "第{$row}行年级错误";
                }
            }
            if (! empty($errMessage)) {
                throw new NormalStatusException(implode(';', $errMessage));
            }
            // 数据处理
            $mobiles = $data->pluck('mobile');
            $userModel = $model->whereIn('mobile', $mobiles)->get();
            $diffMobiles = $mobiles->diff($userModel->pluck('mobile'));
            $newCollection = $data->whereIn('mobile', $diffMobiles);
            foreach ($newCollection as $item) {
                $gradeId = $grade->where('title', $item['grade'])->first()['key'];
                $item['grade_id'] = $gradeId;
                $insertData = $this->handleSaveData($item);
                $model->create($insertData);
            }
            return true;
        };
        return parent::import($dto, $closure);
    }

    /**
     * 处理提交数据.
     * @param mixed $params
     */
    protected function handleData($params): array
    {
        if (! isset($params['orderBy'])) {
            $params['orderBy'] = ['id'];
        }
        if (! isset($params['orderType'])) {
            $params['orderType'] = ['desc'];
        }
        return $params;
    }
}
