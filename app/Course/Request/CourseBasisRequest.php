<?php

declare(strict_types=1);

namespace App\Course\Request;

use Mine\MineFormRequest;

/**
 * 课时详情表验证数据类.
 */
class CourseBasisRequest extends MineFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    public function batchUpdateRules(): array
    {
        return ['ids' => 'required|array'];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '',
            'title' => '标题',
            'subtitle' => '副标题',
            'course_type' => '课程类型：1直播, 4公开课, 5录播课, 7讲座, 8音频课, 9系统课',
            'course_sub_type' => '课程为公开课的时候选择子分类 1最强大脑，2思维导图，3作文',
            'course_classify_id' => '课程分类',
            'sort' => '',
            'price' => '课程价格',
            'origin_price' => '原价',
            'vip_price' => 'vip_price',
            'course_cover' => '封面图片',
            'cover_video' => '视频封面',
            'advance_time' => '提前进入时间',
            'is_free' => '是否免费',
            'is_playback' => '支持回放',
            'is_generated_class' => '生成班级',
            'is_vip_class' => '会员课程,1:要单独购买才能观看',
            'watch_num' => '可观看次数',
            'validity_date' => '视频有效期',
            'start_play_date' => '播放开始时间',
            'end_play_date' => '播放结束时间',
            'start_play_year' => '播放时间年份',
            'sales_num' => '可售数量',
            'sales_base' => '销售基数',
            'browse_base' => '浏览基数',
            'states' => '状态',
            'browse_num' => '浏览量',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'indate' => '有效期，单位天',
            'need_address' => '是否需要地址',
            'is_del' => '是否删除',
            'is_top' => '是否置顶',
            'is_hot' => '是否设置成热门 0不是 1热门',
            'material_name' => '教材名称',
            'note' => '课程说明',
            'class_id' => '班级ID',
            'is_group' => '是否分组直播,0否1是',
            'grade_id' => '年级',
            'subject_id' => '科目',
            'is_deal' => '是否处理过该课程(学习报告使用)',
            'is_signup' => '报名,0不可以1可以',
            'course_title' => '',
            'course_second_title' => '二级分类id',
            'other_img' => '360课程封面',
            'is_playback_type' => '1普通 2超级 3至尊',
            'is_show_pic' => '前端是否显示价格0不显示1显示',
            'season' => '全科班季节分类用 1春,2夏,3秋,4寒',
            'is_show_sub_title' => '前端是否以子标题作为标题显示,0否,1是',
            'vip_type' => '1:优享会员,2:超级会员,3:至尊会员',
            'is_give' => '1:表示是活动赠送的课程,如果学员购买了这个课添加到素养课里面',
            'class_type' => '0:小学中学高中都能查,1小学,2中学,3高中',
        ];
    }
}
