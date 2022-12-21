<?php

declare(strict_types=1);

namespace App\System\Controller\Sms;

use App\System\Request\SmsRequest;
use App\System\Service\SmsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'system/app/sms')]
class SmsController extends MineController
{
    #[Inject]
    protected SmsService $service;

    /**
     * 发送忘记密码验证码
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws NoGatewayAvailableException
     */
    #[GetMapping('getForgotPwdSms')]
    public function getForgotPwdSms(SmsRequest $request): ResponseInterface
    {
        return $this->service->getForgotPwdSms($request->all()) ? $this->success() : $this->error();
    }
}
