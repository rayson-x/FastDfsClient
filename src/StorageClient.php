<?php

namespace Ant\FastDFS;

use SplFileInfo;
use InvalidArgumentException;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Commands\Storage\Upload;
use Ant\FastDFS\Constants\OperationFlag;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Connections\Default\Stream;
use Ant\FastDFS\Protocols\Struct\StorePath;
use Ant\FastDFS\Commands\Storage\GetMetadata;
use Ant\FastDFS\Commands\Storage\SetMetadata;
use Ant\FastDFS\Protocols\Struct\StorageNode;
use Ant\FastDFS\Protocols\Struct\FileMetadataSet;
use Ant\FastDFS\Contracts\Connector as ConnectorContract;

/**
 * @method FileMetadataSet getMetadata(string $group, string $path)
 * 
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
        'upload'      => Upload::class,
        'setMetadata' => SetMetadata::class,
        'getMetadata' => GetMetadata::class,
    ];

    /**
     * 储存节点内容
     *
     * @var StorageNode $node
     */
    protected $storageNode;

    /**
     * @param array $addresses
     * @param ConnectorContract $connector
     * @param MetadataMapper $mapper
     */
    public function __construct(
        StorageNode $node,
        ConnectorContract $connector,
        MetadataMapper $mapper,
    ) {
        $this->storageNode = $node;

        parent::__construct(["{$node->ip}:{$node->port}"], $connector, $mapper);
    }

    /**
     * 上传文件
     * 
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
     * 上传流为文件
     * 
     * @param $resource
     * @return StorePath
     */
    public function uploadStream($resource, string $extension = ''): StorePath
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('数据流不可用');
        }

        rewind($resource);

        return $this->callCommand('upload', [
            $this->storageNode->storeIndex, new Stream($resource), $extension,
        ]);
    }

    /**
     * 上传字符串内容为文件
     * 
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

    /**
     * 新增或覆盖文件元数据
     * 
     * @param string $group
     * @param string $path
     * @return Response
     */
    public function mergeMetadata(string $group, string $path, array $metadata)
    {
        return $this->callCommand('setMetadata', [OperationFlag::MERGE, $group, $path, $metadata]);
    }

    /**
     * 覆写文件元数据
     * 
     * @param string $group
     * @param string $path
     * @return Response
     */
    public function overwriteMetadata(string $group, string $path, array $metadata)
    {
        return $this->callCommand('setMetadata', [OperationFlag::OVERWRITE, $group, $path, $metadata]);
    }
}
