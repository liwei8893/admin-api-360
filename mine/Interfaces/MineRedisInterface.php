<?php

declare(strict_types=1);

namespace Mine\Interfaces;

interface MineRedisInterface
{
    /**
     * 设置 key 类型名.
     */
    public function setTypeName(string $typeName): void;

    /**
     * 获取key 类型名.
     */
    public function getTypeName(): string;
}
