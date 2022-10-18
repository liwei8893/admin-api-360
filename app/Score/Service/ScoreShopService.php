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

namespace App\Score\Service;

use App\Score\Mapper\ScoreShopMapper;
use Mine\Abstracts\AbstractService;

/**
 * 积分管理服务类
 */
class ScoreShopService extends AbstractService
{
    /**
     * @var ScoreShopMapper
     */
    public $mapper;

    public function __construct(ScoreShopMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}