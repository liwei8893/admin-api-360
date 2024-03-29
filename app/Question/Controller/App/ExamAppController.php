<?php

declare(strict_types=1);

namespace App\Question\Controller\App;

use App\Question\Service\ExamService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'question/app/exam')]
class ExamAppController extends MineController
{
    #[Inject]
    protected ExamService $service;

    /**
     * 获取题目列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('batchRead'), Auth('app')]
    public function batchRead(): ResponseInterface
    {
        return $this->success($this->service->batchRead($this->request->all()));
    }
}
