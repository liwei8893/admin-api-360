<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\Talk;
use App\Score\Event\ScoreAddEvent;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\Abstracts\AbstractMapper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 讲一讲审核Mapper类.
 */
class TalkMapper extends AbstractMapper
{
    /**
     * @var Talk
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Talk::class;
    }

    public function voteToggle($id, $userId): array
    {
        $talkModel = $this->model::query()->find($id);
        if (!$talkModel) {
            return [];
        }
        return $talkModel->userVote()->toggle($userId);
    }

    /**
     * 更新一条数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update(int $id, array $data): bool
    {
        $this->filterExecuteAttributes($data, true);
        $model = $this->model::find($id);
        // TODO 添加积分事件
        if (isset($data['status']) && $data['status']) {
            // status 0 表示审核拒绝,1通过,通过时加积分
            event(new ScoreAddEvent('share', $model->user_id, $id));
        }
        return $model->update($data) > 0;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 关联章节表ID
        if (isset($params['course_period_id']) && $params['course_period_id'] !== '') {
            $query->where('course_period_id', '=', $params['course_period_id']);
        }

        // 默认2需要审核,通过为1,拒绝为0
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 视频url
        if (isset($params['url']) && $params['url'] !== '') {
            $query->where('url', '=', $params['url']);
        }

        // 创建时间
        if (isset($params['created_at'][0], $params['created_at'][1])) {
            $query->whereBetween(
                'created_at',
                [strtotime($params['created_at'][0] . ' 00:00:00'), strtotime($params['created_at'][1] . ' 23:59:59')]
            );
        }

        if (!empty($params['withUser'])) {
            $query->with('user:id,user_name,mobile,platform,avatar,avatar_frame');
        }
        if (isset($params['withUserVoteCount']) && $params['withUserVoteCount']) {
            $query->withCount(['userVote', 'userVote as isVote' => function ($query) {
                $query->where('user_id', user('app')->hasLogin() ? user('app')->getId() : 0);
            }]);
        }
        if (!empty($params['withUserNoAudit']) && user('app')->hasLogin()) {
            $query->orWhere(function (Builder $query) use ($params) {
                $query->where('user_id', user('app')->getId())
                    ->when(!empty($params['course_period_id']), function ($query) use ($params) {
                        $query->where('course_period_id', $params['course_period_id']);
                    });
            });
        }
        if (!empty($params['withCoursePeriod'])) {
            return $query->with(['coursePeriod' => function (HasOne $query) {
                $query->with('courseBasis:id,title')->select(['id', 'title', 'course_basis_id']);
            }]);
        }

        return $query;
    }
}
