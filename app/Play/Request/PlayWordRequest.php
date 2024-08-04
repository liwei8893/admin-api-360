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
namespace App\Play\Request;

use Mine\MineFormRequest;

/**
 * 单词游戏验证数据类
 */
class PlayWordRequest extends MineFormRequest
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
            //单词 验证
            'word' => 'required',
            //英式英标 验证
            'uk' => 'required',
            //英式发音 验证
            'uk_speech' => 'required',
            //美式英标 验证
            'us' => 'required',
            //美式发音 验证
            'us_speech' => 'required',
            //中文翻译 验证
            'trs' => 'required',
            //卡片单词 验证
            'word_card' => 'required',

        ];
    }
    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //单词 验证
            'word' => 'required',
            //英式英标 验证
            'uk' => 'required',
            //英式发音 验证
            'uk_speech' => 'required',
            //美式英标 验证
            'us' => 'required',
            //美式发音 验证
            'us_speech' => 'required',
            //中文翻译 验证
            'trs' => 'required',
            //卡片单词 验证
            'word_card' => 'required',

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
            'word' => '单词',
            'uk' => '英式英标',
            'uk_speech' => '英式发音',
            'us' => '美式英标',
            'us_speech' => '美式发音',
            'trs' => '中文翻译',
            'word_card' => '卡片单词',

        ];
    }

}