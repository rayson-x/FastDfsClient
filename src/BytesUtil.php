<?php

namespace Ant\FastDFS;

use Ant\FastDFS\Constants\Common;

/**
 * BytesUtil
 * 
 * @package Ant\FastDFS
 */
class BytesUtil
{
    /**
     * 把双精度数字从int转换为byte
     * 
     * @param int $value
     * @return string
     */
    public static function long2buff(int $value): string
    {
        return pack('NN', $value >> 32, $value & 0xFFFFFFFF);
    }

    /**
     * 把双精度数字从byte转换为int
     * 
     * @param string $value
     * @return int
     */
    public static function buff2long(string $value): int
    {
        $len = strlen($value);
        if ($len < Common::LONG_SIZE) {
            if ($len < Common::INT_SIZE) {
                return 0;
            }

            $value = substr($value, $len - 4, 4);

            return array_values(unpack('N', $value))[0];
        }

        list($hight, $low) = array_values(unpack('N*N*', $value));

        return (int) bcadd($low, bcmul($hight, '4294967296'));
    }

    /**
     * 转换字符串为指定长度的二进制
     * 
     * @param string $value
     * @param int $max
     * @return string
     */
    public static function padding(string $value, int $max): string
    {
        $len = strlen($value);

        return $len > $max ? substr($value, 0, $max) : $value . pack('x' . ($max - $len));
    }
}
