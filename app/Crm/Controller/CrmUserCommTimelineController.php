<?php
declare(strict_types=1);

namespace App\Crm\Controller;

use App\Crm\Request\CrmUserCommTimelineRequest;
use App\Crm\Service\CrmUserCommTimelineService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 用户沟通时间控制器
 * Class CrmUserCommTimelineController
 */
#[Controller(prefix: "crm/userCommTimeline"), Auth]
class CrmUserCommTimelineController extends MineController
{
    /**
     * 业务处理服务
     * CrmUserCommTimelineService
     */
    #[Inject]
    protected CrmUserCommTimelineService $service;

    /**
     * 列表
     * @param CrmUserCommTimelineRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('list')]
    public function list(CrmUserCommTimelineRequest $request): ResponseInterface
    {
        $params = $request->all();
        $params['orderBy'] = ['comm_time'];
        $params['orderType'] = ['asc'];
        return $this->success($this->service->getList($params));
    }
}
