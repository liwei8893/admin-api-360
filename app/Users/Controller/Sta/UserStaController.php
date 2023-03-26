<?php

declare(strict_types=1);

namespace App\Users\Controller\Sta;

use App\Users\Service\UserStaService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'users/sta'), Auth]
class UserStaController extends MineController
{
    #[Inject]
    protected UserStaService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getArealDistribution')]
    public function getArealDistribution(): ResponseInterface
    {
        return $this->success($this->service->getArealDistribution());
    }
}
