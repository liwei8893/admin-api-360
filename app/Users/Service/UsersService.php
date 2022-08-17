<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Users\Service;

use App\System\Service\SystemDeptService;
use App\System\Service\SystemDictDataService;
use App\Users\Mapper\UsersMapper;
use App\Users\Model\Users;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Mine\Helper\LoginUser;

/**
 * 用户表服务类
 */
class UsersService extends AbstractService
{

    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected SystemDictDataService $systemDictDataService;

    #[Inject]
    protected SystemDeptService $systemDeptService;

    #[Inject]
    protected LoginUser $loginUser;

    #[Inject]
    protected UserSalePlatformService $userSalePlatformService;

    /**
     * @var UsersMapper
     */
    #[Inject]
    public $mapper;


    /**
     * 创建用户
     * @param $data
     * @return int
     * author:ZQ
     * time:2022-08-16 16:09
     */
    public function save($data): int
    {
        if ($this->mapper->existsByMobile($data['mobile'])) {
            throw new NormalStatusException('手机号已存在');
        }
        $data = $this->handleSaveData($data);
        return $this->mapper->save($data);
    }


    /**
     * @param array $data
     * @return array
     * author:ZQ
     * time:2022-08-17 10:06
     */
    public function handleSaveData(array $data): array
    {
        // 获取平台编号,挂载到数组
        $data = $this->userSalePlatformService->withPlatformNum($data);
        // 合并初始化参数
        return array_merge([
            'mobile' => $data['mobile'],
            'user_name' => $this->mapper->getInitUserName($data['mobile']),
            'user_nickname' => $this->mapper->getInitUserName($data['mobile']),
            'real_name' => $this->mapper->getInitUserName($data['mobile']),
            'user_pass' => $this->mapper->getInitPassword($data['mobile']),
            'avatar' => config('hxt-app.defaultAvatar'),
            'user_type' => 1,
            'status' => 1,
            'sex' => 3,
            'created_id' => $this->loginUser->getId(),
            'created_name' => $this->loginUser->getUsername(),
        ], $data);
    }

    /**
     * 初始化密码
     * @param int $id
     * @return bool
     * author:ZQ
     * time:2022-06-01 15:23
     */
    public function initUserPassword(int $id): bool
    {
        return $this->mapper->initUserPassword($id);
    }

    /**
     * 处理提交数据
     * @param $params
     * @return array
     */
    protected function handleData($params): array
    {
        if (!isset($params['orderBy'])) {
            $params['orderBy'] = ['id'];
        }
        if (!isset($params['orderType'])) {
            $params['orderType'] = ['desc'];
        }
        return $params;
    }

    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        $params = $this->handleData($params);
        return parent::getPageList($params, $isScope);
    }

    /**
     * 用户导入
     * @param string $dto
     * @param \Closure|null $closure
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * author:ZQ
     * time:2022-08-16 16:16
     */
    #[Transaction]
    public function import(string $dto, ?\Closure $closure = null): bool
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
                if (empty($value['mobile']) || !preg_match("/^1[3456789]\d{9}$/", $value['mobile'])) {
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


}