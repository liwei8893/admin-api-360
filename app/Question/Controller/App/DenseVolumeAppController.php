<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Request\DenseVolumeRequest;
use App\Question\Service\DenseVolumeService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'question/app/denseVolume')]
class DenseVolumeAppController extends MineController
{
    #[Inject]
    protected DenseVolumeService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getPageList')]
    public function getPageList(DenseVolumeRequest $request): ResponseInterface
    {
        $params = $request->all();
        $params['select'] = 'id,month,name,grade,subject,type,answer,new_state,difficulty';
        return $this->success($this->service->getPageList($params, false));
    }

    /**
     * 获取试卷链接.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUrl'), Auth('app')]
    public function getUrl(DenseVolumeRequest $request): ResponseInterface
    {
        // TODO 分科权限验证
        $params = $request->validated();
        return $this->success($this->service->getUrl($params));
    }
}
