<?php

declare(strict_types=1);

namespace App\Pay\Controller\App;

use App\Pay\Service\PayLinkService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
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
}
