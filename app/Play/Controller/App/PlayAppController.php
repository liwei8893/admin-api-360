<?php

namespace App\Play\Controller\App;

use App\Play\Request\PlayAppRequest;
use App\Play\Service\PlayIdiomService;
use App\Play\Service\PlayUserRecordService;
use App\Play\Service\PlayWordService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "play/app")]
class PlayAppController extends MineController
{
    #[Inject]
    protected PlayIdiomService $idiomService;

    #[Inject]
    protected PlayUserRecordService $userRecordService;

    #[Inject]
    protected PlayWordService $wordService;

    /**
     * 获取成语接龙关卡
     * @param PlayAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getIdiom')]
    public function getIdiom(PlayAppRequest $request): ResponseInterface
    {
        $params = $request->all();
        if ($params['id'] > $this->idiomService->getMaxId()) {
            return $this->error("恭喜啦，已经通关了！", 201);
        }
        return $this->success($this->idiomService->read($params['id']));
    }

    /**
     * 获取用户游戏记录
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUserRecord'), Auth('app')]
    public function getUserRecord(): ResponseInterface
    {
        return $this->success($this->userRecordService->readByUserId(user('app')->getId()));
    }

    /**
     * 保存用户游戏记录
     * @param PlayAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('saveUserRecord'), Auth('app')]
    public function saveUserRecord(PlayAppRequest $request): ResponseInterface
    {
        return $this->success($this->userRecordService->saveOrUpdate($request->all()));
    }

    /**
     * @param PlayAppRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getWord')]
    public function getWord(PlayAppRequest $request): ResponseInterface
    {
        $params = $request->all();
        if ($params['id'] > $this->idiomService->getMaxId()) {
            return $this->error("恭喜啦，已经通关了！", 201);
        }
        return $this->success($this->wordService->read($params['id']));
    }
}
