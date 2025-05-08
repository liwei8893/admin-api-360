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

use App\Crm\Model\CrmShop;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 商品管理Mapper类
 */
class CrmShopMapper extends AbstractMapper
{
    /**
     * @var CrmShop
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmShop::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 商品名称
        if (isset($params['shop_name']) && $params['shop_name'] !== '') {
            $query->where('shop_name', 'like', '%'.$params['shop_name'].'%');
        }

        // 商品分类
        if (isset($params['category_id']) && $params['category_id'] !== '') {
            $query->where('category_id', '=', $params['category_id']);
        }

        // 商品价格
        if (isset($params['price']) && $params['price'] !== '') {
            $query->where('price', '=', $params['price']);
        }

        // 商品状态
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', '=', $params['status']);
        }

        return $query;
    }
}