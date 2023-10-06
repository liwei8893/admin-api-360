<?php

declare(strict_types=1);

namespace Mine\Helper;

use Exception;

class Tool
{
    /**
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

    /**
     * 判断操作系统
     * @param $agent $request->getHeader('user-agent')[0]
     */
    public static function os($agent): string
    {
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.1/i', $agent)) {
            return 'Windows 7';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.2/i', $agent)) {
            return 'Windows 8';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 10.0/i', $agent)) {
            return 'Windows 10';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 11.0/i', $agent)) {
            return 'Windows 11';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 5.1/i', $agent)) {
            return 'Windows XP';
        }
        if (stripos($agent, 'linux') !== false) {
            return 'Linux';
        }
        if (stripos($agent, 'mac') !== false) {
            return 'Mac';
        }
        // 添加手机端系统判断
        if (stripos($agent, 'iphone')) {
            return 'iPhone';
        }
        if (stripos($agent, 'android')) {
            return 'Android';
        }
        return '未知';
    }

    /**
     * 判断浏览器.
     * @param $agent $request->getHeader('user-agent')[0]
     */
    public static function browser($agent): string
    {
        if (stripos($agent, 'MSIE') !== false) {
            return 'MSIE';
        }
        if (stripos($agent, 'Edg') !== false) {
            return 'Edge';
        }
        if (stripos($agent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (stripos($agent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (stripos($agent, 'Safari') !== false) {
            return 'Safari';
        }
        if (stripos($agent, 'Opera') !== false) {
            return 'Opera';
        }
        if (stripos($agent, 'micromessenger') !== false) {
            return 'wechat';
        }
        return '未知';
    }
}
