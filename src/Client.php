<?php

namespace Ant\FastDFS;

use InvalidArgumentException;
use Ant\FastDFS\Contracts\Command;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Contracts\Connector as ConnectorContract;

/**
 * Client基类
 *
 * @package Ant\FastDFS
 */
abstract class Client
{
    /**
     * 可用服务器列表
     *
     * @var array
     */
    protected $addresses = [];

    /**
     * 可用Command列表
     *
     * @var array
     */
    protected $commands = [];

    /**
     * 配置
     *
     * @var array
     */
    protected $config = [];

    /**
     * 对象元数据缓存
     *
     * @var ConnectorContract
     */
    protected $connector;

    /**
     * 对象元数据缓存
     *
     * @var MetadataMapper
     */
    protected $mapper;

    /**
     * @param array $addresses
     * @param array $config
     * @param ConnectorContract $connector
     * @param MetadataMapper $mapper
     */
    public function __construct(
        array $addresses,
        array $config,
        ConnectorContract $connector,
        MetadataMapper $mapper,
    ) {
        foreach ($addresses as $address) {
            $this->setServerAddress($address);
        }

        $this->config    = $config;
        $this->mapper    = $mapper;
        $this->connector = $connector;
    }

    /**
     * @param string $address
     */
    public function setServerAddress(string $address)
    {
        if (array_key_exists($address, $this->addresses)) {
            return;
        }

        $parts = parse_url($address);

        if (empty($parts['host']) || empty($parts['port'])) {
            throw new InvalidArgumentException("Given URI \"{$address}\" invalid.");
        }

        $ip = trim($parts['host'], '[]');
        if (false === filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException("Given URI \"{$ip}\" does not contain a valid host IP");
        }

        $this->addresses[$address] = [$parts['host'], $parts['port']];
    }

    /**
     * @param string $name
     * @param string $command
     */
    public function registerCommand(string $name, string $command)
    {
        if (!is_subclass_of($command, Command::class)) {
            throw new InvalidArgumentException('Param 2 must implements Ant\FastDFS\Contracts\Command');
        }

        $this->commands[$name] = $command;
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function callCommand(string $name, array $arguments)
    {
        if (empty($this->commands[$name])) {
            throw new InvalidArgumentException("Command: {$name} not found");
        }

        $command = new $this->commands[$name]($this->mapper);

        $command->setArguments($arguments);

        // TODO 多个服务器根据权重获取
        // 定义一个OrderInterface,允许用户根据需求来设置server优先级
        foreach ($this->addresses as [$host, $port]) {
            $connection = $this->connector->connect($host, $port);

            return $connection->executeCommand($command);
        }

        throw new InvalidArgumentException('No available tracker found');
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        return $this->callCommand($name, $arguments);
    }
}
