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
namespace App\Ai\Request;

use Mine\MineFormRequest;

/**
 * 题目管理验证数据类
 */
class AiQuestionRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    
    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //试题类型 验证
            'ques_type' => 'required',
            //试题题干 验证
            'ques_stem' => 'required',
            //正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置 验证
            'right_answer' => 'required',

        ];
    }
    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //试题类型 验证
            'ques_type' => 'required',
            //试题题干 验证
            'ques_stem' => 'required',
            //正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置 验证
            'right_answer' => 'required',

        ];
    }

    
    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '主键',
            'classify_id' => '分类ID',
            'ques_type' => '试题类型',
            'ques_stem' => '试题题干',
            'right_answer' => '正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置',

        ];
    }

}