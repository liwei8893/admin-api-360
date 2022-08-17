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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function save($data): int
    {
        if ($this->mapper->existsByMobile($data['mobile'])) {
            throw new NormalStatusException('手机号已存在');
        }
        // 初始密码默认手机后六位
        $data['user_pass'] = $this->mapper->getInitPassword($data['mobile']);
        // 如果不传用户名,初始名称为手机号
        if (empty($data['user_name'])) {
            $data['user_name'] = $this->mapper->getInitUserName($data['mobile']);
        }
        $data['user_nickname'] = $data['user_name'];
        $data['real_name'] = $data['user_name'];
        // 默认头像
        $data['avatar'] = config('hxt-app.defaultAvatar');
        // 默认性别保密
        $data['sex'] = 3;
        // 操作人
        $loginUser = $this->container->get(LoginUser::class);
        $data['created_id'] = $loginUser->getId();
        $data['created_name'] = $loginUser->getUsername();
        return $this->mapper->save($data);
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
            $loginUser = $this->container->get(LoginUser::class);
            foreach ($newCollection as $item) {
                $gradeId = $grade->where('title', $item['grade'])->first()['key'];
                $insertData = [
                    'user_name' => $item['user_name'],
                    'user_pass' => $this->mapper->getInitPassword($item['mobile']),
                    'mobile' => $item['mobile'],
                    'avatar' => config('hxt-app.defaultAvatar'),
                    'real_name' => $item['user_name'],
                    'platform' => $item['platform'],
                    'grade_id' => $gradeId,
                    'user_nickname' => $item['user_name'],
                    'remark' => $item['remark'] ?? '',
                    'experience' => 0,
                    'status' => 1,
                    'user_type' => 1,
                    'created_id' => $loginUser->getId(),
                    'created_name' => $loginUser->getUsername(),
                    'score' => 0,
                ];
                $model->create($insertData);
            }
            return true;
        };
        return parent::import($dto, $closure);
    }


}