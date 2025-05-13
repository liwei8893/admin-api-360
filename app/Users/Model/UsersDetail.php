<?php

declare(strict_types=1);

namespace App\Users\Model;

use Carbon\Carbon;
use Mine\MineModel;

/**
 * @property int $id ID
 * @property int $user_id 用户ID
 * @property string $customer_name 客户姓名
 * @property int $customer_identity 客户身份
 * @property int $customer_source 客户来源
 * @property int $customer_level 客户级别
 * @property string $phone1 电话1
 * @property string $phone2 phone2
 * @property int $media 媒体
 * @property int $media_product 媒体产品
 * @property string $address 地址
 * @property string $name 姓名
 * @property array $excellent_subject 优学科
 * @property array $poor_subject 差学科
 * @property array $tags 标签
 * @property string $birthday 生日
 * @property int $gender 性别
 * @property int $learning_attitude 学习态度
 * @property int $age 年龄
 * @property int $grade 年级
 * @property int $is_serious 是否认真
 * @property int $tutoring_class 辅导班
 * @property int $private_tutor 家教
 * @property int $personality 性格
 * @property int $is_living_on_campus 是否住校
 * @property int $no_call 禁止外呼
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class UsersDetail extends MineModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users_detail';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'customer_name', 'customer_identity', 'customer_source', 'customer_level', 'phone1', 'phone2', 'media', 'media_product', 'address', 'name', 'excellent_subject', 'poor_subject', 'tags', 'birthday', 'gender', 'learning_attitude', 'age', 'grade', 'is_serious', 'tutoring_class', 'private_tutor', 'personality', 'is_living_on_campus', 'no_call', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'customer_identity' => 'integer', 'customer_source' => 'integer', 'customer_level' => 'integer', 'media' => 'integer', 'media_product' => 'integer', 'excellent_subject' => 'array', 'poor_subject' => 'array', 'tags' => 'array', 'gender' => 'integer', 'learning_attitude' => 'integer', 'age' => 'integer', 'grade' => 'integer', 'is_serious' => 'integer', 'tutoring_class' => 'integer', 'private_tutor' => 'integer', 'personality' => 'integer', 'is_living_on_campus' => 'integer', 'no_call' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
