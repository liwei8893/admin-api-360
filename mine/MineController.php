<?php


declare(strict_types=1);

namespace Mine;

use Hyperf\Di\Annotation\Inject;
use Mine\Traits\ControllerTrait;

/**
 * 后台控制器基类
 * Class MineController.
 */
abstract class MineController
{
    use ControllerTrait;

    #[Inject]
    protected Mine $mine;
}
