<?php

declare(strict_types=1);

namespace App\Commerce\Controller\App;

use App\Commerce\Request\CommerceCardRequest;
use App\Commerce\Service\CommerceCardService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'commerce/app/card')]
class CommerceCardController extends MineController
{
    #[Inject]
    protected CommerceCardService $cardService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('activateCard')]
    public function activateCard(CommerceCardRequest $request): ResponseInterface
    {
        return $this->cardService->activateCard($request->validated()) ? $this->success() : $this->error();
    }
}
