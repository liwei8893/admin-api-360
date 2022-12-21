<?php

declare(strict_types=1);

namespace Mine\Helper;

use Exception;

class Tool
{
    /**
     * @param int $length
     * @param string $chars
     * @return string
     * @throws Exception
     */
    public static function salt(int $length = 6, string $chars = '0123456789'): string
    {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $hash .= $chars[random_int(0, $max)];
        }
        return $hash;
    }
}
