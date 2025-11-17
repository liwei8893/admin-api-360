<?php
declare(strict_types=1);


namespace App\Users\Service;

use App\Users\Mapper\UserDownloadFileLogMapper;
use Mine\Abstracts\AbstractService;

/**
 * 用户下载文件记录服务类
 */
class UserDownloadFileLogService extends AbstractService
{
    /**
     * @var UserDownloadFileLogMapper
     */
    public $mapper;

    public function __construct(UserDownloadFileLogMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
