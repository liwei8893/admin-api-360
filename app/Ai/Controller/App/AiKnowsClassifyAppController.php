<?php
declare(strict_types=1);

namespace App\Ai\Controller\App;

use App\Ai\Request\AiKnowsClassifyRequest;
use App\Ai\Service\AiKnowsClassifyService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\MineController;
use Psr\Http\Message\ResponseInterface;

/**
 * 知识点分类控制器
 * Class AiKnowsClassifyController
 */
#[Controller(prefix: "ai/app/knowsClassify")]
class AiKnowsClassifyAppController extends MineController
{
    /**
     * 业务处理服务
     * AiKnowsClassifyService
     */
    #[Inject]
    protected AiKnowsClassifyService $service;

    #[GetMapping("getTree")]
    public function getTree(AiKnowsClassifyRequest $request): ResponseInterface
    {
        return $this->success($this->service->getAppTree($request->all()));
    }

    #[GetMapping("getList")]
    public function getList(AiKnowsClassifyRequest $request): ResponseInterface
    {
        return $this->success($this->service->getAppList($request->all()));
    }
}
