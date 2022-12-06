<?php

declare(strict_types=1);

namespace App\Setting\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mine\Annotation\Auth;
use Mine\MineController;

/**
 * setting 公共方法控制器
 * Class CommonController.
 */
#[Controller(prefix: 'setting/common'), Auth]
class CommonController extends MineController
{
    /**
     * 返回模块信息及表前缀
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('getModuleList')]
    public function getModuleList(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->mine->getModuleInfo());
    }
}
