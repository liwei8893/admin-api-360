<?php

declare (strict_types=1);

namespace App\Users\Model;

use App\Order\Model\Order;
use App\Score\Model\Avatar;
use App\System\Model\SystemDictData;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Mine\MineModel;

/**
 * @property int $id
 * @property string $user_name 登录名称
 * @property string $real_name 真实姓名
 * @property string $user_nickname 用户昵称
 * @property string $user_pass 密码
 * @property string $user_email 邮箱
 * @property string $id_card 身份证号
 * @property string $remember_token apitoken
 * @property string $mobile 手机号
 * @property int $status 状态 1启用 0禁用
 * @property int $sex 0 男 1女 3保密
 * @property string $last_login_ip 最后登录时间
 * @property int $last_login_time
 * @property string $birthday 生日
 * @property string $signature 个性签名
 * @property string $avatar 用户头像
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property int $is_buy 用户是否购买 1购买 0未购买
 * @property string $wx_openid 微信openid
 * @property string $qq_openid qq openid
 * @property int $integral 积分
 * @property int $grade_id 年级id
 * @property int $province_id 省
 * @property int $city_id 市
 * @property int $area_id 区
 * @property int $user_type 0内部 1外部
 * @property int $dis_id 分销商的id
 * @property string $identity_card 提现省份证
 * @property string $bank_user_name 提现用户名称
 * @property int $user_from 用户的来源 0:苹果,1:安卓,2:PC,3:微信,4:小程序,5:H5
 * @property int $extension_from_user_id 推广人id
 * @property int $qq QQ
 * @property int $is_teacher 是否为讲师 0:不 1:是
 * @property int $is_assistant 是否为助教 0:不 1:是
 * @property int $is_student 是否为学员 0:不 1:是
 * @property int $attribute_grade_id 关联属性表年级值id
 * @property int $edit_username 是否修改了名字,只能一次
 * @property int $t_type 是否名师,1否2是
 * @property string $sale_platform
 * @property int $headmaster 所属班主任
 * @property int $is_headmaster
 * @property string $parent_name 家长姓名
 * @property string $parent_wx 家长微信
 * @property string $teacher_wx 班主任微信
 * @property string $address 详细地址
 * @property string $remark 备注
 * @property int $yw 语文分数
 * @property int $sx 数学分数
 * @property int $yy 英语分数
 * @property int $wl 物理分数
 * @property int $hx 化学分数
 * @property int $ls 历史分数
 * @property int $dl 地理分数
 * @property int $sw 生物分数
 * @property int $zz 政治分数
 * @property string $platform 所属平台
 * @property int $is_show 是否显示
 * @property string $wxgzh_openid 微信公众号openid
 * @property string $old_platform
 * @property int $is_adviser
 * @property string $order_updated_at
 * @property int $experience 0:正价班，1:199元，5:30元
 * @property string $created_name
 * @property int $created_id
 * @property int $is_playback_type 1:普通会员，2:超级会员，3:至尊会员
 * @property int $contact_time
 * @property string $user_property
 * @property int $days 连续签到天数
 * @property int $score 积分
 * @property int $is_remind 重要提醒
 * @property int $first_month
 * @property string $tag 学生标签
 * @property int $market_id 销售id
 * @property string $remark_case 首月情况备注
 * @property int $is_student_type 1小学 2初中
 * @property string $mp_openid 小程序openid
 */
class Users extends MineModel
{
    /**
     * @description 无会员
     */
    public const VIP_TYPE_NONE = [self::VIP_TYPE_ENJOY, self::VIP_TYPE_SUPER, self::VIP_TYPE_SUPREME];
    /**
     * @description 优享会员
     */
    public const VIP_TYPE_ENJOY = 1355;
    /**
     * @description 超级会员
     */
    public const VIP_TYPE_SUPER = 950;
    /**
     * @description 至尊会员
     */
    public const VIP_TYPE_SUPREME = 1143;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_name', 'real_name', 'user_nickname', 'user_pass', 'user_email', 'id_card', 'remember_token', 'mobile', 'status', 'sex', 'last_login_ip', 'last_login_time', 'birthday', 'signature', 'avatar', 'created_at', 'updated_at', 'is_buy', 'wx_openid', 'qq_openid', 'integral', 'grade_id', 'province_id', 'city_id', 'area_id', 'user_type', 'dis_id', 'identity_card', 'bank_user_name', 'user_from', 'extension_from_user_id', 'qq', 'is_teacher', 'is_assistant', 'is_student', 'attribute_grade_id', 'edit_username', 't_type', 'sale_platform', 'headmaster', 'is_headmaster', 'parent_name', 'parent_wx', 'teacher_wx', 'address', 'remark', 'yw', 'sx', 'yy', 'wl', 'hx', 'ls', 'dl', 'sw', 'zz', 'platform', 'is_show', 'wxgzh_openid', 'old_platform', 'is_adviser', 'order_updated_at', 'experience', 'created_name', 'created_id', 'is_playback_type', 'contact_time', 'user_property', 'days', 'score', 'is_remind', 'first_month', 'tag', 'market_id', 'remark_case', 'is_student_type', 'mp_openid'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'status' => 'string', 'sex' => 'string', 'last_login_time' => 'integer', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s', 'is_buy' => 'string', 'integral' => 'integer', 'grade_id' => 'string', 'province_id' => 'integer', 'city_id' => 'integer', 'area_id' => 'integer', 'user_type' => 'string', 'dis_id' => 'integer', 'user_from' => 'integer', 'extension_from_user_id' => 'integer', 'qq' => 'integer', 'is_teacher' => 'integer', 'is_assistant' => 'integer', 'is_student' => 'integer', 'attribute_grade_id' => 'integer', 'edit_username' => 'integer', 't_type' => 'integer', 'headmaster' => 'integer', 'is_headmaster' => 'integer', 'yw' => 'integer', 'sx' => 'integer', 'yy' => 'integer', 'wl' => 'integer', 'hx' => 'integer', 'ls' => 'integer', 'dl' => 'integer', 'sw' => 'integer', 'zz' => 'integer', 'is_show' => 'integer', 'is_adviser' => 'integer', 'experience' => 'integer', 'created_id' => 'integer', 'is_playback_type' => 'integer', 'contact_time' => 'integer', 'days' => 'integer', 'score' => 'integer', 'is_remind' => 'integer', 'first_month' => 'integer', 'market_id' => 'integer', 'is_student_type' => 'integer'];
    protected $dateFormat = 'U';
    protected $hidden = ['user_pass', 'remember_token', 'wx_openid', 'wxgzh_openid', 'mp_openid'];

    /**
     * 验证密码
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function passwordVerify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * 关联订单表
     * @return \Hyperf\Database\Model\Relations\HasMany
     * author:ZQ
     * time:2022-05-29 16:56
     */
    public function orders(): \Hyperf\Database\Model\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * 关联年级表
     * @return \Hyperf\Database\Model\Relations\HasOne
     * author:ZQ
     * time:2022-05-29 17:43
     */
    public function grades(): \Hyperf\Database\Model\Relations\HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'grade_id')
            ->where('code', 'grade')->where('status', MineModel::ENABLE);
    }

    public function status(): \Hyperf\Database\Model\Relations\HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'status')
            ->where('code', 'data_status')->where('status', MineModel::ENABLE);
    }

    public function userType(): \Hyperf\Database\Model\Relations\HasOne
    {
        return $this->hasOne(SystemDictData::class, 'value', 'user_type')
            ->where('code', 'userType')->where('status', MineModel::ENABLE);
    }

    public function vipType()
    {
        return $this->hasOne(Order::class, 'user_id', 'id')
            ->select(['user_id', 'id'])
            ->selectRaw('CASE
           WHEN shop_id = ? THEN 1
           WHEN shop_id = ? THEN 2
           WHEN shop_id = ? THEN 3
           ELSE 0 END AS vipType', [self::VIP_TYPE_ENJOY, self::VIP_TYPE_SUPER, self::VIP_TYPE_SUPREME])
            ->selectRaw('CASE
           WHEN shop_id = ? THEN "优享会员"
           WHEN shop_id = ? THEN "超级会员"
           WHEN shop_id = ? THEN "至尊会员"
           ELSE 0 END AS vipName', [self::VIP_TYPE_ENJOY, self::VIP_TYPE_SUPER, self::VIP_TYPE_SUPREME])
            ->selectRaw("DATE_ADD(FROM_UNIXTIME(created_at, '%Y-%m-%d'), INTERVAL indate DAY) AS endDate")
            ->NormalOrder()->IsNotExpire()->orderBy('vipType', 'desc');
    }

    /**
     * 密码加密
     * @param $value
     * @return void
     */
    public function setUserPassAttribute($value): void
    {
        $this->attributes['user_pass'] = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * 关联用户头像表
     * @return BelongsToMany
     */
    public function avatarTable(): BelongsToMany
    {
        return $this->belongsToMany(Avatar::class, 'user_avatar', 'user_id', 'avatar_id');
    }

    /**
     * 用户头像获取器
     * @param $value
     * @return string
     * author:ZQ
     * time:2022-07-04 12:22
     */
    public function getAvatarAttribute($value): string
    {
        return (!str_contains($value, 'https')) ? config('file.storage.qiniu.domain') . '/' . $value : $value;
    }
}