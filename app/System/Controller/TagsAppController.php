<?php

declare(strict_types=1);

namespace App\System\Controller;

use App\System\Request\TagsRequest;
use App\System\Service\TagsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'system/app/tag')]
class TagsAppController extends MineController
{
    #[Inject]
    protected TagsService $service;

    /**
     * 获取所以tag.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getTags')]
    public function getTags(TagsRequest $request): ResponseInterface
    {
        $params = $request->all();
        $params['select'] = 'id,name';
        $params['status'] = 1;
        return $this->success($this->service->getList($params, false));
    }
}
