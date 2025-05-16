<?php


declare(strict_types=1);

namespace Mine\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 禁止重复提交.
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Resubmit extends AbstractAnnotation
{
    /**
     * @param int $second 限制时间（秒）
     * @param null|string $message 提示信息
     */
    public function __construct(public int $second = 3, public ?string $message = null)
    {
    }
}
