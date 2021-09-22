<?php

namespace Ant\FastDFS\Constants;

/**
 * @package Ant\FastDFS\Constants
 */
class Common
{
    // byte类型长度只占1字节
    public const BYTE_SIZE = 1;
    // 长整型类型长度占8字节
    public const LONG_SIZE = 8;
    // int类型长度占4字节
    public const INT_SIZE = 4;

    // 文件组名长度
    public const GROUP_NAME_SIZE = 16;
    // IP地址长度
    public const IP_ADRESS_SIZE = 16;

    // 文件扩展名长度
    public const FILE_EXT_NAME_MAX_SIZE = 6;
    // 文件前缀长度
    public const FILE_PREFIX_MAX_SIZE = 16;

    // 域名长度
    public const DOMAIN_NAME_MAX_SIZE = 128;
    // 版本占位长度
    public const VERSION_SIZE = 6;
    // Storage server id
    public const STORAGE_ID_MAX_SIZE = 16;
}
