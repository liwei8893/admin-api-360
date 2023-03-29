<?php

declare(strict_types=1);

namespace App\Sta\Controller;

use App\Sta\Dto\CourseRecordDto;
use App\Sta\Dto\HasCourseRecordDto;
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
        return $this->success($this->service->getHasCourseRecord($this->request->all()));
    }

    /**
     * 续费统计
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('orderRenew'), Permission('sta:orderRenew')]
    public function orderRenew(): ResponseInterface
    {
        return $this->success($this->service->getHasCourseRecord($this->request->all()));
    }

    /**
     * 退费统计
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('orderRefund'), Permission('sta:orderRefund')]
    public function orderRefund(): ResponseInterface
    {
        return $this->success($this->service->getHasCourseRecord($this->request->all()));
    }
}
