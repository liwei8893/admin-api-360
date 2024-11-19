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

namespace App\Ai\Service;

use App\Ai\Mapper\AiQuesDetailStatMapper;
use Mine\Abstracts\AbstractService;

/**
 * 题目详情统计服务类
 */
class AiQuesDetailStatService extends AbstractService
{
    /**
     * @var AiQuesDetailStatMapper
     */
    public $mapper;

    public function __construct(AiQuesDetailStatMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}