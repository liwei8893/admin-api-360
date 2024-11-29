<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Ai\Controller\App;

use App\Ai\Request\AiAssessReportRequest;
use App\Ai\Service\AiAssessReportService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 评测报告控制器
 * Class AiAssessReportController
 */
#[Controller(prefix: "ai/app/assessReport")]
class AiAssessReportAppController extends MineController
{
    /**
     * 业务处理服务
     * AiAssessReportService
     */
    #[Inject]
    protected AiAssessReportService $service;

    /**
     * 获取分页列表
     * @param AiAssessReportRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("getPageList"), Auth('app')]
    public function getPageList(AiAssessReportRequest $request): ResponseInterface
    {
        return $this->success($this->service->getAppPageList($request->all()));
    }

    /**
     * 生成评测报告
     * @param AiAssessReportRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("gen"), Auth('app')]
    public function gen(AiAssessReportRequest $request): ResponseInterface
    {
        return $this->success($this->service->gen($request->all()));
    }

    /**
     * 获取评测报告详情
     * @param AiAssessReportRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("getOne")]
    public function getOne(AiAssessReportRequest $request): ResponseInterface
    {
        return $this->success($this->service->getOne((int)$request->input('id')));
    }

    /**
     * 完成评测报告
     * @param AiAssessReportRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("finish"), Auth('app')]
    public function finish(AiAssessReportRequest $request): ResponseInterface
    {
        return $this->service->finish($request->all()) ? $this->success() : $this->error();
    }

    /**
     * @param AiAssessReportRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("getKDA")]
    public function getKDA(AiAssessReportRequest $request): ResponseInterface
    {
        return $this->success($this->service->getKDA((int)$request->input('id')));
    }
}
