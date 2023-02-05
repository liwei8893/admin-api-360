<?php

declare(strict_types=1);

namespace App\Course\Controller\App;

use App\Course\Request\SunRequest;
use App\Course\Request\TalkRequest;
use App\Course\Service\SunService;
use App\Course\Service\TalkService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'course/app/comment')]
class CourseCommentAppController extends MineController
{
    #[Inject]
    protected SunService $sunService;

    #[Inject]
    protected TalkService $talkService;

    /**
     * 讲一讲列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('talkPageList'), Auth('app')]
    public function talkPageList(TalkRequest $request): ResponseInterface
    {
        return $this->success($this->talkService->getAppPageList($request->all()));
    }

    /**
     * 保存讲一讲.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('talkSave'), Auth('app')]
    public function talkSave(TalkRequest $request): ResponseInterface
    {
        $userId = user('app')->getId();
        $params = $request->validated();
        $params['user_id'] = $userId;
        $params['created_by'] = $userId;
        $params['updated_by'] = $userId;
        return $this->talkService->save($params) ? $this->success() : $this->error();
    }

    /**
     * 切换点赞.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('talkVote'), Auth('app')]
    public function talkVote(TalkRequest $request): ResponseInterface
    {
        return $this->success($this->talkService->vote($request->all()));
    }

    /**
     * 删除讲一讲.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('talkDelete'), Auth('app')]
    public function talkDelete(TalkRequest $request): ResponseInterface
    {
        $ids = $request->input('id');
        return $this->talkService->delete((array)$ids) ? $this->success() : $this->error();
    }

    /**
     * 晒一晒列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('sunPageList'), Auth('app')]
    public function sunPageList(SunRequest $request): ResponseInterface
    {
        return $this->success($this->sunService->getAppPageList($request->all()));
    }

    /**
     * 保存晒一晒.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('sunSave'), Auth('app')]
    public function sunSave(SunRequest $request): ResponseInterface
    {
        $userId = user('app')->getId();
        $params = $request->validated();
        $params['user_id'] = $userId;
        $params['created_by'] = $userId;
        $params['updated_by'] = $userId;
        return $this->talkService->save($params) ? $this->success() : $this->error();
    }

    /**
     * 切换点赞.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('sunVote'), Auth('app')]
    public function sunVote(SunRequest $request): ResponseInterface
    {
        return $this->success($this->sunService->vote($request->all()));
    }

    /**
     * 删除讲一讲.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('sunDelete'), Auth('app')]
    public function sunDelete(SunRequest $request): ResponseInterface
    {
        $ids = $request->input('id');
        return $this->talkService->delete((array)$ids) ? $this->success() : $this->error();
    }
}
