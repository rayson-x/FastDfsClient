<?php

namespace Ant\FastDFS\Connections;

use InvalidArgumentException;
use Ant\FastDFS\Contracts\Connection;
use Ant\FastDFS\Contracts\Connector as ConnectorContract;
use Ant\FastDFS\Connections\Default\Connection as DefaultConnection;

/**
 * 连接器
 * TODO
 * 支持设置多种连接方式切换(reactphp,swoole)
 *
 * @package Ant\FastDFS
 */
class Connector implements ConnectorContract
{
    /**
     * @var string
     */
    protected string $driver;

    /**
     * @var array
     */
    protected array $config;

    /**
     * @param string $driver
     * @param array $config
     */
    public function __construct(string $driver, array $config = [])
    {
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(string $address, int $port): Connection
    {
        $driverMethod = 'create' . ucfirst($this->driver) . 'Connection';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($address, $port, $this->config);
        } else {
            throw new InvalidArgumentException("Driver [{$this->driver}] is not supported.");
        }
    }

    /**
     * 获取默认连接
     * 
     * @param string $address
     * @param int $port
     * @param array $config
     * @return DefaultConnection
     */
    public function createDefaultConnection(string $address, int $port, array $config): DefaultConnection
    {
        return new DefaultConnection(
            $address,
            $port,
            $config['timeout'] ?? 30,
            $config['socket_context'] ?? [],
        );
    }
}
