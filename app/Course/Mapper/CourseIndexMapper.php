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

namespace App\Course\Mapper;

use App\Course\Model\CourseIndex;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 体验课管理Mapper类
 */
class CourseIndexMapper extends AbstractMapper
{
    /**
     * @var CourseIndex
     */
    public $model;

    public function assignModel()
    {
        $this->model = CourseIndex::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 课程名称
        if (isset($params['course_name']) && $params['course_name'] !== '') {
            $query->where('course_name', '=', $params['course_name']);
        }

        // 副标题
        if (isset($params['sub_title']) && $params['sub_title'] !== '') {
            $query->where('sub_title', '=', $params['sub_title']);
        }

        // 排序
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', '=', $params['sort']);
        }

        // 图片地址
        if (isset($params['img_url']) && $params['img_url'] !== '') {
            $query->where('img_url', '=', $params['img_url']);
        }

        // 视频地址
        if (isset($params['video_url']) && $params['video_url'] !== '') {
            $query->where('video_url', '=', $params['video_url']);
        }

        // 类型名称
        if (isset($params['type_name']) && $params['type_name'] !== '') {
            $query->where('type_name', '=', $params['type_name']);
        }

        // 类型
        if (isset($params['type']) && $params['type'] !== '') {
            $query->where('type', '=', $params['type']);
        }

        // 手机跳转
        if (isset($params['nav_to']) && $params['nav_to'] !== '') {
            $query->where('nav_to', '=', $params['nav_to']);
        }

        // pc图片地址
        if (isset($params['pc_img_url']) && $params['pc_img_url'] !== '') {
            $query->where('pc_img_url', '=', $params['pc_img_url']);
        }

        // 年级
        if (isset($params['grade']) && $params['grade'] !== '') {
            $query->where('grade', '=', $params['grade']);
        }

        // 科目
        if (isset($params['subject']) && $params['subject'] !== '') {
            $query->where('subject', '=', $params['subject']);
        }

        return $query;
    }
}