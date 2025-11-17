<?php
declare(strict_types=1);


namespace App\Users\Mapper;

use App\Users\Model\UserDownloadFileLog;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户下载文件记录Mapper类
 */
class UserDownloadFileLogMapper extends AbstractMapper
{
    /**
     * @var UserDownloadFileLog
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = UserDownloadFileLog::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 章节ID
        if (isset($params['periods_id']) && $params['periods_id'] !== '') {
            $query->where('periods_id', '=', $params['periods_id']);
        }

        // 文件ID
        if (isset($params['file_id']) && $params['file_id'] !== '') {
            $query->where('file_id', '=', $params['file_id']);
        }

        // 文件名称
        if (isset($params['file_name']) && $params['file_name'] !== '') {
            $query->where('file_name', 'like', '%' . $params['file_name'] . '%');
        }

        // 章节名称
        if (isset($params['periods_name']) && $params['periods_name'] !== '') {
            $query->where('periods_name', 'like', '%' . $params['periods_name'] . '%');
        }

        // 课程名称
        if (isset($params['course_name']) && $params['course_name'] !== '') {
            $query->where('course_name', 'like', '%' . $params['course_name'] . '%');
        }

        return $query;
    }
}
