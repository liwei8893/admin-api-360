<?php

declare(strict_types=1);

namespace App\Crm\Model;

use Mine\MineModel;

/**
 * @property int $id ID
 * @property string $caller 坐席号码，仅API自动外呼有此参数
 * @property string $callee 被叫号码
 * @property string $task_id 自动外呼任务ID，仅API自动外呼有此参数
 * @property int $status 状态码，1为呼叫成功，0为呼叫失败,2为呼叫中
 * @property string $status_info 挂断方信息、呼叫状态信息和SIP响应状态码，中间用英文逗号隔开，辅助排查故障
 * @property int $duration 通话时长，大于等于0的整数，单位为秒
 * @property string $return_uuid 通话唯一标识。
 * @property string $record_url 录音地址，记录到CRM系统的通话记录，点击可以播放。呼叫失败则为空
 * @property int $create_time 执行呼叫的时间戳
 * @property int $api_date 时间戳
 */
class CrmCallRecord extends MineModel
{
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'crm_call_record';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'caller', 'callee', 'task_id', 'status', 'status_info', 'duration', 'return_uuid', 'record_url', 'create_time', 'api_date'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'duration' => 'integer', 'create_time' => 'integer', 'api_date' => 'integer'];
}
