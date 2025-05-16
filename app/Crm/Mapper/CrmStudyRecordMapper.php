<?php
declare(strict_types=1);


namespace App\Crm\Mapper;

use App\Crm\Model\CrmStudyRecord;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 学习记录Mapper类
 */
class CrmStudyRecordMapper extends AbstractMapper
{
    /**
     * @var CrmStudyRecord
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmStudyRecord::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 自增主键
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 姓名
        if (isset($params['name']) && $params['name'] !== '') {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        // 电话
        if (isset($params['phone']) && $params['phone'] !== '') {
            $query->where('phone', 'like', '%' . $params['phone'] . '%');
        }

        // 辅导老师
        if (isset($params['tutor_teacher']) && $params['tutor_teacher'] !== '') {
            $query->where('tutor_teacher', 'like', '%' . $params['tutor_teacher'] . '%');
        }

        // 销售老师
        if (isset($params['sales_teacher']) && $params['sales_teacher'] !== '') {
            $query->where('sales_teacher', 'like', '%' . $params['sales_teacher'] . '%');
        }

        // 主讲老师
        if (isset($params['main_teacher']) && $params['main_teacher'] !== '') {
            $query->where('main_teacher', 'like', '%' . $params['main_teacher'] . '%');
        }

        // 年级
        if (isset($params['grade']) && $params['grade'] !== '') {
            $query->where('grade', 'like', '%' . $params['grade'] . '%');
        }

        // 备注
        if (isset($params['remark']) && $params['remark'] !== '') {
            $query->where('remark', 'like', '%' . $params['remark'] . '%');
        }

        // 课程名称
        if (isset($params['course_name']) && $params['course_name'] !== '') {
            $query->where('course_name', 'like', '%' . $params['course_name'] . '%');
        }

        // 课次名称
        if (isset($params['lesson_name']) && $params['lesson_name'] !== '') {
            $query->where('lesson_name', 'like', '%' . $params['lesson_name'] . '%');
        }

        // 进教室时间
        if (isset($params['enter_class_time']) && is_array($params['enter_class_time']) && count($params['enter_class_time']) == 2) {
            $query->whereBetween(
                'enter_class_time',
                [$params['enter_class_time'][0], $params['enter_class_time'][1]]
            );
        }

        // 离开教室时间
        if (isset($params['leave_class_time']) && is_array($params['leave_class_time']) && count($params['leave_class_time']) == 2) {
            $query->whereBetween(
                'leave_class_time',
                [$params['leave_class_time'][0], $params['leave_class_time'][1]]
            );
        }

        // 直播时长
        if (isset($params['live_duration']) && $params['live_duration'] !== '') {
            $query->where('live_duration', 'like', '%' . $params['live_duration'] . '%');
        }

        // 回放时长
        if (isset($params['playback_duration']) && $params['playback_duration'] !== '') {
            $query->where('playback_duration', 'like', '%' . $params['playback_duration'] . '%');
        }

        // 互动次数
        if (isset($params['interaction_count']) && $params['interaction_count'] !== '') {
            $query->where('interaction_count', '=', $params['interaction_count']);
        }

        return $query;
    }
}
