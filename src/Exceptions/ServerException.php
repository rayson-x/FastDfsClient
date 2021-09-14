<?php

namespace Ant\FastDFS\Exceptions;

use Ant\FastDFS\Constants\ErrorCode;

class ServerException extends FastDFSException
{
    protected static $errors = [
        ErrorCode::NOT_EXISTS         => '文件或目录不存在',
        ErrorCode::IO_ERROR           => '服务端发生io异常',
        ErrorCode::SERVER_BUSY        => '服务端繁忙',
        ErrorCode::INVALID_PARAM      => '无效的参数',
        ErrorCode::NOT_ENOUGH_SPACE   => '没有足够的存储空间',
        ErrorCode::CONNECTION_REFUSED => '服务端拒绝连接',
        ErrorCode::FILE_EXISTS        => '文件已经存在',
    ];

    /**
     * @param int $code
     * @return string|null
     */
    public static function getErrorMessage(int $code): ?string
    {
        return static::$errors[$code] ?? null;
    }

    /**
     * @param int $code
     * @param string $message
     */
    public static function setErrorCode(int $code, string $message)
    {
        static::$errors[$code] = $message;
    }

    /**
     * @param int $code
     * @return ServerException
     */
    public static function byCode(int $code): ServerException
    {
        $msg = static::getErrorMessage($code);

        if ($msg === null) {
            $msg = "错误码: {$code}";
        } else {
            $msg = "错误码: {$code}, 错误信息: {$msg}";
        }

        return new ServerException($msg, $code);
    }
}
