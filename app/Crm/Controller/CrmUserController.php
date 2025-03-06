<?php

namespace App\Crm\Controller;

use App\Crm\Request\CrmUserRequest;
use App\Crm\Service\CrmUserService;
use App\System\Service\SystemUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "crm/user"), Auth]
class CrmUserController extends MineController
{
    #[Inject]
    protected CrmUserService $service;

    #[Inject]
    protected SystemUserService $systemUserService;

    /**
     * 用户列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('crm:user:index')]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 获取系统用户列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('systemUserIndex'), Permission('crm:user:batchDistro')]
    public function systemUserIndex(): ResponseInterface
    {
        return $this->success($this->service->systemUserIndex($this->request->all()));
    }

    /**
     * 批量分配用户给老师.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('batchDistro'), Permission('crm:user:batchDistro')]
    public function batchDistro(CrmUserRequest $request): ResponseInterface
    {
        return $this->service->batchDistro($request->all()) ? $this->success() : $this->error();
    }

    /**
     * 用户详情
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('detail/{id}'), Permission('crm:user:detail')]
    public function detail(int $id): ResponseInterface
    {
        return $this->success($this->service->detail($id));
    }

    /**
     * 保存用户信息.
     * @param CrmUserRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('saveDetail')]
    public function saveDetail(CrmUserRequest $request): ResponseInterface
    {
        return $this->success($this->service->saveDetail($request->all()));
    }
}
