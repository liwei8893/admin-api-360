<?php

namespace App\Crm\Controller;

use App\Crm\Service\CrmStaService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "crm/sta"), Auth]
class CrmStaController extends MineController
{
    #[Inject]
    protected CrmStaService $service;

    /**
     * 获取简报看板
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getBriefingBoard')]
    public function getBriefingBoard(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->success($this->service->getBriefingBoard($params));
    }

    /**
     * 按月获取每日跟进数
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getFollowUpNumByDate')]
    public function getFollowUpNumByDate(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->success($this->service->getFollowUpNumByDate($params));
    }

    /**
     * 转化统计个人维度
     */
    #[GetMapping('getConversionStaByPersonal')]
    public function getConversionStaByPersonal(): ResponseInterface
    {
        $params = $this->request->all();
        return $this->success($this->service->getConversionStaByPersonal($params));
    }
}
