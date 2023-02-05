<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\Sun;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Relations\HasOne;
use Mine\Abstracts\AbstractMapper;

/**
 * 晒一晒Mapper类.
 */
class SunMapper extends AbstractMapper
{
    /**
     * @var Sun
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Sun::class;
    }

    public function voteToggle($id, $userId): array
    {
        $talkModel = $this->model::query()->find($id);
        if (! $talkModel) {
            return [];
        }
        return $talkModel->userVote()->toggle($userId);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 关联章节表ID
        if (isset($params['course_period_id']) && $params['course_period_id'] !== '') {
            $query->where('course_period_id', '=', $params['course_period_id']);
        }

        // 默认2需要审核,通过为1,不通过为0
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        // 富文本内容
        if (isset($params['html']) && $params['html'] !== '') {
            $query->where('html', '=', $params['html']);
        }
        if (! empty($params['withUser'])) {
            $query->with('user:id,user_name,mobile,platform,avatar,avatar_frame');
        }
        if (isset($params['withUserVoteCount']) && $params['withUserVoteCount']) {
            $query->withCount(['userVote', 'userVote as isVote' => function ($query) {
                $query->where('user_id', user('app')->hasLogin() ? user('app')->getId() : 0);
            }]);
        }
        if (! empty($params['withUserNoAudit']) && user('app')->hasLogin()) {
            $query->orWhere(function (Builder $query) use ($params) {
                $query->where('user_id', user('app')->getId())
                    ->when(! empty($params['course_period_id']), function ($query) use ($params) {
                        $query->where('course_period_id', $params['course_period_id']);
                    });
            });
        }
        if (! empty($params['withCoursePeriod'])) {
            return $query->with(['coursePeriod' => function (HasOne $query) {
                $query->with('courseBasis:id,title')->select(['id', 'title', 'course_basis_id']);
            }]);
        }

        return $query;
    }
}
