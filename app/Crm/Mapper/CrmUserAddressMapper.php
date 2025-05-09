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

namespace App\Crm\Mapper;

use App\Crm\Model\CrmUserAddress;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户地址信息Mapper类
 */
class CrmUserAddressMapper extends AbstractMapper
{
    /**
     * @var CrmUserAddress
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmUserAddress::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 用户
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }
        // 收货人姓名
        if (isset($params['consignee']) && $params['consignee'] !== '') {
            $query->where('consignee', 'like', '%' . $params['consignee'] . '%');
        }

        // 收货人联系电话
        if (isset($params['phone']) && $params['phone'] !== '') {
            $query->where('phone', 'like', '%' . $params['phone'] . '%');
        }

        // 省份
        if (isset($params['province']) && $params['province'] !== '') {
            $query->where('province', 'like', '%' . $params['province'] . '%');
        }

        // 城市
        if (isset($params['city']) && $params['city'] !== '') {
            $query->where('city', 'like', '%' . $params['city'] . '%');
        }

        // 区县
        if (isset($params['area']) && $params['area'] !== '') {
            $query->where('area', 'like', '%' . $params['area'] . '%');
        }

        // 详细地址
        if (isset($params['detail_address']) && $params['detail_address'] !== '') {
            $query->where('detail_address', 'like', '%' . $params['detail_address'] . '%');
        }

        // 邮政编码
        if (isset($params['postal_code']) && $params['postal_code'] !== '') {
            $query->where('postal_code', 'like', '%' . $params['postal_code'] . '%');
        }

        // 是否为默认地址，0 表示非默认，1 表示默认
        if (isset($params['is_default']) && $params['is_default'] !== '') {
            $query->where('is_default', '=', $params['is_default']);
        }

        return $query;
    }
}
