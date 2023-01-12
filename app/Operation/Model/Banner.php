<?php

declare(strict_types=1);

namespace App\Operation\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id
 * @property int $banner_type_id banner端： 1 pc  2 h5  3 小程序  4 app 5图书
 * @property string $banner_img 图片路径
 * @property int $sorc 排序
 * @property Carbon $created_at 创建时间
 * @property int $states 0展示,1删除
 * @property Carbon $updated_at 修改时间
 * @property string $title 标题
 * @property string $link 链接
 * @property int $status 是否启用的状态(0启用，1不启用)
 * @property int $link_type 图片对应的范围 1课程 7讲座9套餐 10图书 11文库
 * @property int $link_style 跳转类型1链接2商品ID
 * @property int $link_local banner位置，1首页，2图书
 */
class Banner extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'banner';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'banner_type_id', 'banner_img', 'sorc', 'created_at', 'states', 'updated_at', 'title', 'link', 'status', 'link_type', 'link_style', 'link_local'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'banner_type_id' => 'integer', 'sorc' => 'integer', 'created_at' => 'datetime', 'states' => 'integer', 'updated_at' => 'datetime', 'status' => 'integer', 'link_type' => 'integer', 'link_style' => 'integer', 'link_local' => 'integer'];

    protected ?string $dateFormat = 'U';
}
