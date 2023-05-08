<?php

declare(strict_types=1);

namespace App\Pay\Controller\App;

use App\Pay\Request\PayAppRequest;
use App\Pay\Service\PayAppService;
use App\Pay\Service\PayLinkService;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use JsonException;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'pay/app')]
class PayAppController extends MineController
{
    #[Inject]
    protected PayLinkService $payLinkService;

    #[Inject]
    protected PayAppService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException|NotFoundExceptionInterface
     */
    #[GetMapping('getPayLinkConfig/{id}')]
    public function getPayLinkConfig(int $id): ResponseInterface
    {
        $model = $this->payLinkService->read($id);
        if (! $model) {
            return $this->error();
        }
        $model['view_config'] = json_decode($model['view_config'], false, 512, JSON_THROW_ON_ERROR);
        return $this->success($model);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[PostMapping('OAuths')]
    public function OAuths(PayAppRequest $request): ResponseInterface
    {
        return $this->success($this->service->OAuths($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('payLinkVip')]
    public function payLinkVip(PayAppRequest $request): ResponseInterface
    {
        return $this->success($this->service->payLinkVip($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('wxNotify/{id}')]
    public function wxNotify(int $id): ResponseInterface
    {
        return $this->service->wxNotify($id);
    }
}
