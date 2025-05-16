<?php
declare(strict_types=1);


namespace App\Ai\Controller\App;

use App\Ai\Request\AiAssessQuesDetailRequest;
use App\Ai\Service\AiAssessQuesDetailService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 评测题目明细控制器
 * Class AiAssessQuesDetailController
 */
#[Controller(prefix: "ai/app/assessQuesDetl")]
class AiAssessQuesDetailAppController extends MineController
{
    /**
     * 业务处理服务
     * AiAssessQuesDetailService
     */
    #[Inject]
    protected AiAssessQuesDetailService $service;


    /**
     * 更新
     * @param AiAssessQuesDetailRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("submit"), Auth('app')]
    public function submit(AiAssessQuesDetailRequest $request): ResponseInterface
    {
        return $this->service->submit($request->validated()) ? $this->success() : $this->error();
    }
}
