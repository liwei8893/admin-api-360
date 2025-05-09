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

use App\Crm\Mapper\CrmShopOrderMapper;
use App\Crm\Model\CrmShop;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * 订单管理服务类
 */
class CrmShopOrderService extends AbstractService
{
    /**
     * @var CrmShopOrderMapper
     */
    public $mapper;

    #[Inject]
    protected CrmUserTimelineService $userTimelineService;

    public function __construct(CrmShopOrderMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     * @param array $data
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function save(array $data): int
    {
        $data = $this->handleData($data);
        $shopMod = CrmShop::query()->where('id', $data['shop_id'])->first();
        if (!$shopMod) {
            throw new NormalStatusException('商品不存在');
        }
        $createdAdminId = user()->getId();
        $createdAdminName = user()->getNickname();
        $this->userTimelineService->saveBuyShopEvent($data['user_id'], $createdAdminId, "[{$createdAdminName}]出单[{$shopMod->shop_name}]");
        return parent::save($data);
    }

    /**
     * 更新一条数据.
     * @param int $id
     * @param array $data
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function update(int $id, array $data): bool
    {
        $data = $this->handleData($data);
        return parent::update($id, $data);
    }


    /**
     * @param array $data
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    protected function handleData(array $data): array
    {
        if (empty($data['created_by'])) {
            $data['created_by'] = user()->getId();
        }
        // 如果没有订单id，则生成订单id
        if (empty($data['order_number'])) {
            $data['order_number'] = snowflake_id();
        }
        return $data;
    }
}
