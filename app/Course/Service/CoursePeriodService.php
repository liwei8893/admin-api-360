<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CoursePeriodMapper;
use App\Course\Model\CourseBasis;
use App\Course\Model\CoursePeriod;
use App\Order\Model\Order;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\SubjectAuth;
use Mine\Exception\NormalStatusException;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class CoursePeriodService extends AbstractService
{
    /**
     * @var CoursePeriodMapper
     */
    #[Inject]
    public $mapper;

    #[SubjectAuth]
    public function getUrl(int $id): array
    {
        /* @var CoursePeriod $model */
        $model = $this->mapper->read($id);
        if (! $model) {
            throw new NormalStatusException('章节不存在!');
        }
        $model->makeVisible(['qiniu_url']);
        $courseModel = $model->courseBasis;
        if (! $courseModel) {
            throw new NormalStatusException('课程不存在!');
        }
        $grade = $courseModel->basisGrade->pluck('key')->toArray();
        // 增加点击量
        ++$model->hits;
        ++$model->real_hits;
        $model->save();
        return ['url' => $model['qiniu_url'], 'subject' => $courseModel['subject_id'], 'grade' => $grade, 'courseId' => $courseModel['id'], 'periodId' => $model->id];
    }

    /**
     * @param mixed $params
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function getPlanMonth(array $params): Collection|array
    {
        // 处理季节 1,2月对应寒假2节课 3,4,5,6月对应春季4节课 7,8月对应暑假4节课， 9,10,11,12月对应秋季4节课
        $seasonMap = ['', 4, 4, 1, 1, 1, 1, 2, 2, 3, 3, 3, 3];
        $params['season'] = $seasonMap[$params['month'] ?? date('n')];
        // 处理limit
        $params['limit'] = $params['season'] === 4 ? 2 : 4;
        // 处理offset
        $offsetMap = [0, 0, 2, 0, 4, 8, 12, 0, 4, 0, 4, 8, 12];
        $params['offset'] = $offsetMap[$params['month']];
        // 科目默认语文
        $params['subject_id'] = $params['subject_id'] ?? 3;
        return $this->mapper->getPlanMonth($params);
    }

    public function getSearch(array $ids): Collection|array
    {
        $params = [
            'tagId' => $ids,
            'select' => ['id', 'title', 'course_basis_id'],
            'courseStatus' => CourseBasis::STATUS_NORMAL,
        ];
        // 默认不搜索未上架的课程,如果已经购买就搜索
        $isLogin = user('app')->hasLogin();
        if ($isLogin) {
            $userId = user('app')->getId();
            /* @var Collection $orderIds */
            $params['courseId'] = Order::query()->where('user_id', $userId)->normalOrder()->isNotExpire()->pluck('shop_id');
        }
        $course = $this->mapper->getListCollect($params);
        $course->load(['courseBasis:id,title,course_title', 'tags:id,name']);
        return $course;
    }

    public function getPeriod(int $id): array
    {
        $period = $this->mapper->first(['id' => $id], CoursePeriod::COMMON_FIELDS);
        if (! $period) {
            return [];
        }
        $period->load(['tags']);
        return $period->toArray();
    }
}
