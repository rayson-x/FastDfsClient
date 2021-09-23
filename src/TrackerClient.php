<?php

namespace Ant\FastDFS;

use Ant\FastDFS\Connections\Connector;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Commands\Tracker\GetGroups;
use Ant\FastDFS\Commands\Tracker\GetStorages;
use Ant\FastDFS\Protocols\Struct\StorageNode;
use Ant\FastDFS\Commands\Tracker\DeleteStorage;
use Ant\FastDFS\Protocols\Struct\GroupStateList;
use Ant\FastDFS\Commands\Tracker\GetFetchStorage;
use Ant\FastDFS\Commands\Tracker\GetStoreStorage;
use Ant\FastDFS\Protocols\Struct\StorageNodeInfo;
use Ant\FastDFS\Protocols\Struct\StorageStateList;
use Ant\FastDFS\Contracts\Connector as ConnectorContract;
use Ant\FastDFS\Commands\Tracker\GetStoreStorageWithGroup;

/**
 * @method StorageNode getStoreStorage()
 * @method StorageNode getStoreStorageWithGroup(string $group)
 * @method StorageNodeInfo getFetchStorage(string $group, string $path)
 * @method viod deleteStorage(string $group, string $ip)
 * @method GroupStateList getGroups()
 * @method StorageStateList getStorages(string $group, ?string $ip = null)
 * 
 * Tracker客户端,主要负责获取Storage服务器信息
 * 
 * @package Ant\FastDFS
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
        'getFetchStorage'          => GetFetchStorage::class,
        'deleteStorage'            => DeleteStorage::class,
        'getGroups'                => GetGroups::class,
        'getStorages'              => GetStorages::class,
    ];

    /**
     * @param array $servers
     * @param ConnectorContract $connector
     */
    public function __construct(array $servers, ConnectorContract $connector = null)
    {
        $connector = $connector ?: new Connector('default');

        parent::__construct($servers, $connector, new MetadataMapper());
    }

    /**
     * 获取可用的存储客户端
     * 
     * @return StorageClient
     */
    public function getStorageClient(): StorageClient
    {
        $node = $this->getStoreStorage();

        return new StorageClient($node, $this->connector, $this->mapper);
    }

    /**
     * 根据分组名获取可用的存储客户端
     * 
     * @param string $group
     * @return StorageClient
     */
    public function getStorageClientWithGroup(string $group): StorageClient
    {
        $node = $this->getStoreStorageWithGroup($group);

        return new StorageClient($node, $this->connector, $this->mapper);
    }

    /**
     * 根据文件分组以及路径获取断点续传的Client
     * 
     * @param string $group
     * @param string $path
     * @return StorageClient
     */
    public function getAppendClient(string $group, string $path): StorageClient
    {
        $node = $this->callCommand('getFetchStorage', [$group, $path]);

        return new StorageClient($node, $this->connector, $this->mapper);
    }
}
