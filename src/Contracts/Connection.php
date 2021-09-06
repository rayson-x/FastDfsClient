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
     * 获取输入流
     * 
     * @return Stream
     */
    public function getInputStream(): Stream;

    /**
     * 获取输出流
     * 
     * @return Stream
     */
    public function getOutputStream(): Stream;

    /**
     * 关闭连接
     * 
     * @return void
     */
    public function close(): void;

    /**
     * 是否关闭连接
     * 
     * @return bool
     */
    public function isClosed(): bool;

    /**
     * 连接是否有效
     * 
     * @return bool
     */
    public function isValid(): bool;
}
