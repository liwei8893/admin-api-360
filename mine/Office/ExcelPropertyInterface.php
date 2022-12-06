<?php

declare(strict_types=1);

namespace Mine\Office;

interface ExcelPropertyInterface
{
    public function import(\Mine\MineModel $model, ?\Closure $closure = null): bool;

    public function export(string $filename, array|\Closure $closure): \Psr\Http\Message\ResponseInterface;
}
