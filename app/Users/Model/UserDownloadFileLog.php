<?php

declare(strict_types=1);

namespace App\Users\Model;

use Mine\MineModel;

/**
 * @property int $id 
 * @property int $user_id 用户ID
 * @property int $periods_id 章节ID
 * @property int $file_id 文件ID
 * @property string $file_name 文件名称
 * @property string $periods_name 章节名称
 * @property string $course_name 课程名称
 * @property \Carbon\Carbon $created_at 
 */
class UserDownloadFileLog extends MineModel
{
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_download_file_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'periods_id', 'file_id', 'file_name', 'periods_name', 'course_name', 'created_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'periods_id' => 'integer', 'file_id' => 'integer', 'created_at' => 'datetime'];
}
