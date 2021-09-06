<?php

namespace Ant\FastDFS\Contracts;

/**
 * @package Ant\FastDFS\Contracts
 */
interface Stream
{
    /**
     * 读取指定长度的内容
     *
     * @param int $length
     * @return string
     */
    public function read(int $length): string;

    /**
     * 写入数据
     *
     * @param string $byte
     * @return int
     */
    public function write(string $byte): int;

    /**
     * 是否结束
     *
     * @return bool
     */
    public function eof(): bool;

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
     * 获取流的长度
     *
     * @return int|null
     */
    public function getSize(): ?int;

    /**
     * 获取流的元数据
     * 
     * @return array|string|null
     */
    public function getMetadata($key = null): array|string|null;
}
