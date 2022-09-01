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

namespace App\Question\Service;

use App\Question\Mapper\QuestionHistoryMapper;
use Mine\Abstracts\AbstractService;

/**
 * 错题表服务类
 */
class QuestionHistoryService extends AbstractService
{
    /**
     * @var QuestionHistoryMapper
     */
    public $mapper;

    public function __construct(QuestionHistoryMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}