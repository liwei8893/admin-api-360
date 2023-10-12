<?php

declare(strict_types=1);

namespace App\Sta\Controller;

use App\Sta\Dto\CourseHitsDto;
use App\Sta\Dto\CourseRecordDto;
use App\Sta\Dto\HasCourseRecordDto;
use App\Sta\Dto\OrderAddDto;
use App\Sta\Dto\OrderRefundDto;
use App\Sta\Dto\OrderRenewDto;
use App\Sta\Dto\orderSummarySumDto;
use App\Sta\Dto\PeriodHitsDto;
use App\Sta\Service\StaService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\Permission;
use Mine\MineController;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'sta'), Auth]
class StaController extends MineController
{
    #[Inject]
    public StaService $service;

    /**
     * 用户总数.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('users/total')]
    public function getUsersTotal(): ResponseInterface
    {
        return $this->success($this->service->getUsersTotal($this->request->all()));
    }

    /**
     * 课程点击量.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hits/course'), Permission('sta:hits:course')]
    public function courseHits(): ResponseInterface
    {
        return $this->success($this->service->getCourseHits($this->request->all()));
    }

    /**
     * 课程点击量.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hits/course/{id}'), Permission('sta:hits:course')]
    public function courseHitsDetail(int $id): ResponseInterface
    {
        return $this->success($this->service->getCourseHitsDetail($id));
    }

    /**
     * 课程点击量导出.
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('hits/course/export'), Permission('sta:hits:course')]
    public function courseHitsExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getCourseHitsExport($params, CourseHitsDto::class, '课程点击量导出');
    }

    /**
     * 章节点击量.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hits/period'), Permission('sta:hits:period')]
    public function periodHits(): ResponseInterface
    {
        return $this->success($this->service->getPeriodHits($this->request->all()));
    }

    /**
     * 课程点击量导出.
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('hits/period/export'), Permission('sta:hits:period')]
    public function periodHitsExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getPeriodHitsExport($params, PeriodHitsDto::class, '章节点击量导出');
    }

    /**
     * 听课统计
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('courseRecord'), Permission('sta:courseRecord')]
    public function courseRecord(): ResponseInterface
    {
        return $this->success($this->service->getCourseRecord($this->request->all()));
    }

    /**
     * 听课统计导出.
     * @throws ContainerExceptionInterface
     * @throws Exception|NotFoundExceptionInterface
     */
    #[PostMapping('courseRecord/export'), Permission('sta:courseRecord')]
    public function courseRecordExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getCourseRecordExport($params, CourseRecordDto::class, '听课统计导出');
    }

    /**
     * 是否听课统计
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hasCourseRecord'), Permission('sta:hasCourseRecord')]
    public function hasCourseRecord(): ResponseInterface
    {
        return $this->success($this->service->getHasCourseRecord($this->request->all()));
    }

    /**
     * 是否听课统计导出.
     * @throws ContainerExceptionInterface
     * @throws Exception|NotFoundExceptionInterface
     */
    #[PostMapping('hasCourseRecord/export'), Permission('sta:hasCourseRecord')]
    public function hasCourseRecordExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getHasCourseRecordExport($params, HasCourseRecordDto::class, '是否听课统计导出');
    }

    /**
     * 报名统计
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('orderAdd'), Permission('sta:orderAdd')]
    public function orderAdd(): ResponseInterface
    {
        return $this->success($this->service->getOrderAdd($this->request->all()));
    }

    /**
     * 报名统计导出.
     * @throws ContainerExceptionInterface
     * @throws Exception|NotFoundExceptionInterface
     */
    #[PostMapping('orderAdd/export'), Permission('sta:orderAdd')]
    public function orderAddExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getOrderAddExport($params, OrderAddDto::class, '报名统计导出');
    }

    /**
     * 续费统计
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('orderRenew'), Permission('sta:orderRenew')]
    public function orderRenew(): ResponseInterface
    {
        return $this->success($this->service->getOrderRenew($this->request->all()));
    }

    /**
     * 续费统计导出.
     * @throws ContainerExceptionInterface
     * @throws Exception|NotFoundExceptionInterface
     */
    #[PostMapping('orderRenew/export'), Permission('sta:orderRenew')]
    public function orderRenewExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getOrderRenewExport($params, OrderRenewDto::class, '续费统计导出');
    }

    /**
     * 退费统计
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('orderRefund'), Permission('sta:orderRefund')]
    public function orderRefund(): ResponseInterface
    {
        return $this->success($this->service->getOrderRefund($this->request->all()));
    }

    /**
     * 续费统计导出.
     * @throws ContainerExceptionInterface
     * @throws Exception|NotFoundExceptionInterface
     */
    #[PostMapping('orderRefund/export'), Permission('sta:orderRefund')]
    public function orderRefundExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getOrderRefundExport($params, OrderRefundDto::class, '退费统计导出');
    }

    #[GetMapping('orderSummarySum'), Permission('sta:orderSummary:sum')]
    public function orderSummarySum(): ResponseInterface
    {
        return $this->success($this->service->getOrderSummarySum($this->request->all()));
    }

    /**
     * 续费统计导出.
     * @throws ContainerExceptionInterface
     * @throws Exception|NotFoundExceptionInterface
     */
    #[PostMapping('orderSummarySum/export'), Permission('sta:orderSummary:sum')]
    public function orderSummarySumExport(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->service->getOrderSummarySumExport($params, orderSummarySumDto::class, '核单数量统计导出');
    }
}
