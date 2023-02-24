<?php

declare(strict_types=1);

namespace App\System\Crontab;

use App\System\Model\SystemDept;
use Hyperf\DbConnection\Db;

class FillPlatformCode
{
    public function execute(): string
    {
        $msg = '';
        // 查询正常的平台
        $platformCode = SystemDept::query()
            ->where('status', 1)
            ->withCount('platformCode')
            ->get();
        // platform_code_count
        // 遍历查询每个平台剩余code数量,小于1000填充到1000
        foreach ($platformCode as $platform) {
            $count = $platform->platform_code_count;
            $code = $platform->platform;
            $num = 1000 - $count;
            if ($num > 1) {
                $msg .= $code . '-->' . $num . PHP_EOL;
                $this->insetPlatformCode($code, $num);
            }
        }
        return $msg;
    }

    protected function insetPlatformCode($code, $num): void
    {
        for ($i = 1; $i <= $num; ++$i) {
            DB::insert('INSERT INTO user_sale_platform(user_platform, u_sale_platform)
            SELECT a, IF(b IS NULL, 1001, b) AS b
            FROM (SELECT ? AS a, MAX(u_sale_platform) + 1 b FROM user_sale_platform WHERE user_platform = ?) o', [
                $code, $code,
            ]);
        }
    }
}
