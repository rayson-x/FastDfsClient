<?php

namespace Ant\FastDFS\Constants;

/**
 * @package Ant\FastDFS\Constants
 */
class ErrorCode
{
    // 成功
    public const SUCCESS = 0;
    // 文件或目录不存在
    public const NOT_EXISTS = 2;
    // 服务端发生io异常
    public const IO_ERROR = 5;
    // 服务端忙
    public const SERVER_BUSY = 16;
    // 无效的参数
    public const INVALID_PARAM = 22;
    // 没有足够的存储空间
    public const NOT_ENOUGH_SPACE = 28;
    // 服务端拒绝连接
    public const CONNECTION_REFUSED = 61;
    // 文件已经存在
    public const FILE_EXISTS = 114;
}
