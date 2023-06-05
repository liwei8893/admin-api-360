<?php

declare(strict_types=1);

namespace App\Operation\Model;

use Mine\MineModel;

/**
 * @property int $id
 * @property string $tmp_id 模板id
 * @property string $title 课程标题
 * @property string $first 参数
 * @property string $keyword1 参数一
 * @property string $keyword2 参数二
 * @property string $keyword3 参数三
 * @property string $remark remark
 * @property int $status 0未发送 1已发送
 * @property int $send_time 发送时间
 * @property int $create_time 创建时间
 */
class WxMsg extends MineModel
{
    public const SENT = 1;

    public const UNSENT = 0;

    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wx_msg';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'tmp_id', 'title', 'first', 'keyword1', 'keyword2', 'keyword3', 'remark', 'status', 'send_time', 'create_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'send_time' => 'integer', 'create_time' => 'integer'];

    protected array $dates = ['send_time', 'create_time'];

    protected ?string $dateFormat = 'U';
}
