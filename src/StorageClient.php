<?php

namespace Ant\FastDFS;

use SplFileInfo;
use InvalidArgumentException;
use Ant\FastDFS\Commands\Storage\Upload;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Connections\Default\Stream;
use Ant\FastDFS\Protocols\Struct\StorePath;
use Ant\FastDFS\Protocols\Struct\StorageNode;
use Ant\FastDFS\Contracts\Connector as ConnectorContract;

/**
 * @package Ant\FastDFS
 */
class StorageClient extends Client
{
    /**
     * 可用Command列表
     *
     * @var array
     */
    protected $commands = [
        'upload' => Upload::class,
    ];

    /**
     * 储存节点内容
     *
     * @var StorageNode $node
     */
    protected $storageNode;

    /**
     * @param array $addresses
     * @param array $config
     * @param ConnectorContract $connector
     * @param MetadataMapper $mapper
     */
    public function __construct(
        StorageNode $node,
        array $config,
        ConnectorContract $connector,
        MetadataMapper $mapper,
    ) {
        $this->storageNode = $node;

        parent::__construct(["{$node->ip}:{$node->port}"], $config, $connector, $mapper);
    }

    /**
     * @param string $filename
     * @return StorePath
     */
    public function uploadFile(string $filename): StorePath
    {
        $fileInfo = new SplFileInfo($filename);

        if (!$fileInfo->isFile() || !$fileInfo->isReadable()) {
            throw new InvalidArgumentException('文件不存在或不可读');
        }

        $stream = new Stream(fopen($fileInfo->getPathname(), 'r'));

        return $this->callCommand('upload', [
            $this->storageNode->storeIndex, $stream, $fileInfo->getExtension(),
        ]);
    }

    /**
     * @param string $buffer
     * @return StorePath
     */
    public function uploadBuffer(string $buffer, string $extension = ''): StorePath
    {
        $resource = fopen('php://temp', 'w+');

        fwrite($resource, $buffer);

        rewind($resource);

        return $this->callCommand('upload', [
            $this->storageNode->storeIndex, new Stream($resource), $extension,
        ]);
    }
}
