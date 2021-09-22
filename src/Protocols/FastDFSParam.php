<?php

namespace Ant\FastDFS\Protocols;

use Attribute;

/**
 * FastDFSParam
 * 
 * @package Ant\FastDFS\Protocols
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class FastDFSParam
{
    /**
     * 字符串类型
     * 
     * @var int
     */
    public const TYPE_STRING = 0;

    /**
     * 正整数
     * 
     * @var int
     */
    public const TYPE_INT = 1;

    /**
     * byte
     * 
     * @var int
     */
    public const TYPE_BYTE = 2;

    /**
     * 布尔类型
     * 
     * @var int
     */
    public const TYPE_BOOL = 3;

    /**
     * 允许为空的字段
     * 
     * @var int
     */
    public const TYPE_NULLABLE = 4;

    /**
     * 文件属性
     * 
     * @var int
     */
    public const TYPE_FILE_META = 5;

    /**
     * 剩余全部内容
     * 
     * @var int
     */
    public const TYPE_ALL_REST_BYTE = 6;

    /**
     * @param int $index
     * @param int $max
     * @param int $type
     */
    public function __construct(
        public int $type,
        public int $index = 0,
        public int $max = 0,
    ) {

    }

    /**
     * 是否为动态类型
     * 
     * @param int $type
     * @return bool
     */
    public static function isDynamicField(int $type): bool
    {
        return in_array($type, [
            static::TYPE_NULLABLE,
            static::TYPE_FILE_META,
            static::TYPE_ALL_REST_BYTE,
        ]);
    }
}
