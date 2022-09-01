<?php

declare (strict_types=1);
namespace App\Question\Model;

use Mine\MineModel;
/**
 * @property int $id 文件ID
 * @property int $ques_id 试题ID
 * @property string $file_name 文件名称
 * @property string $file_path 文件路径(视频/音频/)
 * @property string $file_type 文件类型:图片，音频，视频
 * @property string $file_size 文件大小(保留两位小数)
 * @property int $created_id 创建人
 * @property \Carbon\Carbon $created_at 创建时间
 * @property int $updated_id 修改人
 * @property \Carbon\Carbon $updated_at 修改时间
 */
class QuesUploadFile extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ques_upload_file';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'ques_id', 'file_name', 'file_path', 'file_type', 'file_size', 'created_id', 'created_at', 'updated_id', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'ques_id' => 'integer', 'file_size' => 'decimal:2', 'created_id' => 'integer', 'created_at' => 'datetime', 'updated_id' => 'integer', 'updated_at' => 'datetime'];
}