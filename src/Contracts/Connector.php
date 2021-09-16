<?php

namespace Ant\FastDFS\Contracts;

/**
 * Connector interface
 * 
 * 不同的连接方式用不同的connector处理
 * 要实现连接池,单独定义一个PoolConnector实现
 *
 * @package Ant\FastDFS\Contracts
 */
interface Connector
{
    /**
     * 连接目标服务器
     *
     * @param string $address
     * @param int $port
     * @return Connection
     */
    public function connect(string $address, int $port): Connection;
}
