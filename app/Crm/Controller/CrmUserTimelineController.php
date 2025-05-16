<?php
declare(strict_types=1);


namespace App\Crm\Controller;

use App\Crm\Request\CrmUserTimelineRequest;
use App\Crm\Service\CrmUserTimelineService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 用户时间线记录表控制器
 * Class CrmUserTimelineController
 */
#[Controller(prefix: "crm/userTimeline"), Auth]
class CrmUserTimelineController extends MineController
{
    /**
     * 业务处理服务
     * CrmUserTimelineService
     */
    #[Inject]
    protected CrmUserTimelineService $service;

    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("list"), Permission("crm:userTimeline, crm:userTimeline:index")]
    public function list(): ResponseInterface
    {
        return $this->success($this->service->getList($this->request->all()));
    }

    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("crm:userTimeline, crm:userTimeline:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 新增
     * @param CrmUserTimelineRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("crm:userTimeline:save"), OperationLog]
    public function save(CrmUserTimelineRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 读取数据
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Permission("crm:userTimeline:read")]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

}
