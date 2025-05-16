<?php
declare(strict_types=1);


namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 话单记录验证数据类
 */
class CrmCallRecordRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    public function callRules(): array
    {
        return [
            'callee' => 'required',
        ];
    }


    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => 'ID',
            'caller' => '坐席号码，仅API自动外呼有此参数',
            'callee' => '被叫号码',
            'status' => '状态码，1为呼叫成功，0为呼叫失败,2为呼叫中',
            'duration' => '通话时长，大于等于0的整数，单位为秒',
            'return_uuid' => '通话唯一标识。',

        ];
    }

}
