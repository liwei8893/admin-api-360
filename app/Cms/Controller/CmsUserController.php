<?php

namespace App\Cms\Controller;

use App\Cms\Request\CmsUserRequest;
use App\Cms\Service\CmsUserService;
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

#[Controller(prefix: "cms/user"), Auth]
class CmsUserController extends MineController
{
    #[Inject]
    protected CmsUserService $service;

    #[Inject]
    protected SystemUserService $systemUserService;

    /**
     * 用户列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('cms:user:index')]
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
    #[GetMapping('systemUserIndex'), Permission('cms:user:batchDistro')]
    public function systemUserIndex(): ResponseInterface
    {
        return $this->success($this->service->systemUserIndex($this->request->all()));
    }

    /**
     * 批量分配用户给老师.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('batchDistro'), Permission('cms:user:batchDistro')]
    public function batchDistro(CmsUserRequest $request): ResponseInterface
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
    #[GetMapping('detail/{id}'), Permission('cms:user:detail')]
    public function detail(int $id): ResponseInterface
    {
        return $this->success($this->service->detail($id));
    }

    /**
     * 保存用户信息.
     * @param CmsUserRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('saveDetail')]
    public function saveDetail(CmsUserRequest $request): ResponseInterface
    {
        return $this->success($this->service->saveDetail($request->all()));
    }
}
