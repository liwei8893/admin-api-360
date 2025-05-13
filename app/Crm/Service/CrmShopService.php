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

namespace App\Crm\Service;

use App\Crm\Mapper\CrmShopMapper;
use Mine\Abstracts\AbstractService;

/**
 * 商品管理服务类
 */
class CrmShopService extends AbstractService
{
    /**
     * @var CrmShopMapper
     */
    public $mapper;

    public function __construct(CrmShopMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
