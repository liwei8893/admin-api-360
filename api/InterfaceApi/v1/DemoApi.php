<?php

declare(strict_types=1);

namespace Api\InterfaceApi\v1;

use App\System\Mapper\SystemDeptMapper;
use App\System\Mapper\SystemUserMapper;
use Mine\MineResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 演示，测试专用.
 */
class DemoApi
{
    protected SystemUserMapper $user;

    protected SystemDeptMapper $dept;

    protected MineResponse $response;

    /**
     * DemoApi constructor.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(SystemUserMapper $user, SystemDeptMapper $dept)
    {
        $this->response = container()->get(MineResponse::class);
        $this->user = $user;
        $this->dept = $dept;
    }

    /**
     * 获取用户列表接口.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUserList(): ResponseInterface
    {
        // 第二个参数，不进行数据权限检查，否则会拉起检测是否登录。
        return $this->response->success('请求成功', $this->user->getPageList([], false));
    }

    /**
     * 获取部门列表接口.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDeptList(): ResponseInterface
    {
        // 第二个参数，不进行数据权限检查，否则会拉起检测是否登录。
        return $this->response->success('请求成功', $this->dept->getTreeList([], false));
    }
}
