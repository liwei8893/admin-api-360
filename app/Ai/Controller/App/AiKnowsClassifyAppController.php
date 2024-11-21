<?php
declare(strict_types=1);

namespace App\Ai\Controller\App;

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
    public function getTree(): ResponseInterface
    {
        return $this->success($this->service->getAppTree());
    }


}
