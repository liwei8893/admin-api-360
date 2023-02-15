<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);

namespace Mine\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * excel导入导出元数据。
 * @Annotation
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ExcelProperty extends AbstractAnnotation
{
    /**
     * 列表头名称.
     */
    public string $value;

    /**
     * 列顺序.
     */
    public int $index;

    /**
     * 自定义字段,支持点语法.
     */
    public string $customField;

    /**
     * 宽度.
     */
    public int $width;

    /**
     * 对齐方式，默认居左.
     */
    public string $align;

    /**
     * 列表头字体颜色.
     */
    public string $headColor;

    /**
     * 列表头背景颜色.
     */
    public string $headBgColor;

    /**
     * 列表体字体颜色.
     */
    public string $color;

    /**
     * 列表体背景颜色.
     */
    public string $bgColor;

    /**
     * 字典数据列表.
     */
    public ?array $dictData = null;

    /**
     * 字典名称.
     */
    public string $dictName;
    /**
     * 数据路径 用法: object.value
     * @var string
     */
    public string $path;
}
