<?php

namespace Ant\FastDFS\Contracts;

/**
 * Connection interface
 * 
 * @package Ant\FastDFS\Contracts
 */
interface Connection
{
    /**
     * 获取输入输出流
     * 
     * @return Stream
     */
    public function getStream(): Stream;

    /**
     * 关闭连接
     * 
     * @return void
     */
    public function close(): void;

    /**
     * 连接是否有效
     * 
     * @return bool
     */
    public function isValid(): bool;
}
