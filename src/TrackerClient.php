<?php

namespace Ant\FastDFS;

use Ant\FastDFS\Connections\Connector;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Protocols\Response\StorageNode;
use Ant\FastDFS\Commands\Tracker\GetStoreStorage;
use Ant\FastDFS\Contracts\Connector as ConnectorContract;
use Ant\FastDFS\Commands\Tracker\GetStoreStorageWithGroup;

/**
 * @method StorageNode getStoreStorage()
 * @method StorageNode getStoreStorageWithGroup(string $group)
 */
class TrackerClient extends Client
{
    /**
     * 可用Command列表
     *
     * @var array
     */
    protected $commands = [
        'getStoreStorage'          => GetStoreStorage::class,
        'getStoreStorageWithGroup' => GetStoreStorageWithGroup::class,
    ];

    /**
     * @param array $servers
     * @param array $config
     * @param ConnectorContract $connector
     */
    public function __construct(
        array $servers,
        array $config = [],
        ConnectorContract $connector = null,
    ) {
        $connector = $connector ?: new Connector('default');

        parent::__construct($servers, $config, $connector, new MetadataMapper());
    }

    /**
     * @return StorageClient
     */
    public function getStorageClient(): StorageClient
    {
        $node = $this->getStoreStorage();

        return $this->getStorageClientFrom($node);
    }

    /**
     * @param string $group
     * @return StorageClient
     */
    public function getStorageClientWithGroup(string $group): StorageClient
    {
        $node = $this->getStoreStorageWithGroup($group);

        return $this->getStorageClientFrom($node);
    }

    /**
     * @param StorageNode $node
     * @return StorageClient
     */
    protected function getStorageClientFrom(StorageNode $node): StorageClient
    {
        return new StorageClient(
            ["{$node->ip}:{$node->port}"], $this->config, $this->connector, $this->mapper
        );
    }
}
