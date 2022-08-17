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

namespace App\Order\Service;

use App\Order\Mapper\OrderMapper;
use Mine\Abstracts\AbstractService;

/**
 * 订单管理服务类
 */
class OrderService extends AbstractService
{
    /**
     * @var OrderMapper
     */
    public $mapper;

    public function __construct(OrderMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}