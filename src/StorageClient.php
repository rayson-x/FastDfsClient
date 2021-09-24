<?php

namespace Ant\FastDFS;

use SplFileInfo;
use InvalidArgumentException;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Commands\Storage\Query;
use Ant\FastDFS\Commands\Storage\Append;
use Ant\FastDFS\Commands\Storage\Delete;
use Ant\FastDFS\Commands\Storage\Upload;
use Ant\FastDFS\Constants\OperationFlag;
use Ant\FastDFS\Commands\Storage\Modify;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Commands\Storage\Truncate;
use Ant\FastDFS\Protocols\Struct\FileInfo;
use Ant\FastDFS\Commands\Storage\Download;
use Ant\FastDFS\Connections\Default\Stream;
use Ant\FastDFS\Protocols\Struct\StorePath;
use Ant\FastDFS\Commands\Storage\GetMetadata;
use Ant\FastDFS\Commands\Storage\SetMetadata;
use Ant\FastDFS\Protocols\Struct\StorageNode;
use Ant\FastDFS\Protocols\Struct\DownloadData;
use Ant\FastDFS\Protocols\Struct\FileMetadataSet;
use Ant\FastDFS\Commands\Storage\UploadAppendable;
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
        'query'            => Query::class,
        'upload'           => Upload::class,
        'delete'           => Delete::class,
        'append'           => Append::class,
        'modify'           => Modify::class,
        'truncate'         => Truncate::class,
        'download'         => Download::class,
        'setMetadata'      => SetMetadata::class,
        'getMetadata'      => GetMetadata::class,
        'uploadAppendable' => UploadAppendable::class,
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
     * @param bool $appendalbe
     * @return StorePath
     */
    public function uploadFile(string $filename, bool $appendalbe = false): StorePath
    {
        $fileInfo = new SplFileInfo($filename);

        if (!$fileInfo->isFile() || !$fileInfo->isReadable()) {
            throw new InvalidArgumentException('文件不存在或不可读');
        }

        $stream = new Stream(fopen($fileInfo->getPathname(), 'r'));

        return $this->callCommand($appendalbe ? 'uploadAppendable' : 'upload', [
            $this->storageNode->storeIndex, $stream, $fileInfo->getExtension(),
        ]);
    }

    /**
     * 上传字符串内容为文件
     *
     * @param string $buffer
     * @param string $extension
     * @param bool $appendalbe
     * @return StorePath
     */
    public function uploadBuffer(
        string $buffer,
        string $extension = '',
        bool $appendalbe = false
    ): StorePath {
        $resource = fopen('php://temp', 'w+');

        fwrite($resource, $buffer);

        return $this->uploadStream($resource, $extension, $appendalbe);
    }

    /**
     * 上传流为文件
     *
     * @param resource $resource
     * @param string $extension
     * @param bool $appendalbe
     * @return StorePath
     */
    public function uploadStream(
        $resource,
        string $extension = '',
        bool $appendalbe = false
    ): StorePath {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('数据流不可用');
        }

        rewind($resource);

        return $this->callCommand($appendalbe ? 'uploadAppendable' : 'upload', [
            $this->storageNode->storeIndex, new Stream($resource), $extension,
        ]);
    }

    /**
     * 追加文件内容到支持断点续传的文件
     * 文件必须支持断点续传(uploadAppendable命令创建的文件)
     *
     * @param string $path
     * @param string $filename
     * @return Response
     */
    public function appendFile(string $path, string $filename)
    {
        $fileInfo = new SplFileInfo($filename);

        if (!$fileInfo->isFile() || !$fileInfo->isReadable()) {
            throw new InvalidArgumentException('文件不存在或不可读');
        }

        $stream = new Stream(fopen($fileInfo->getPathname(), 'r'));

        return $this->callCommand('append', [$path, $stream]);
    }

    /**
     * 追加字符串内容到支持断点续传的文件
     * 文件必须支持断点续传(uploadAppendable命令创建的文件)
     *
     * @param string $path
     * @param string $buffer
     * @return Response
     */
    public function appendBuffer(string $path, string $buffer)
    {
        $resource = fopen('php://temp', 'w+');

        fwrite($resource, $buffer);

        return $this->appendStream($path, $resource);
    }

    /**
     * 追加流数据到支持断点续传的文件
     * 文件必须支持断点续传(uploadAppendable命令创建的文件)
     *
     * @param string $path
     * @param resource $resource
     * @return Response
     */
    public function appendStream(string $path, $resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('数据流不可用');
        }

        rewind($resource);

        return $this->callCommand('append', [$path, new Stream($resource)]);
    }

    /**
     * 根据传入的文件去修改支持断点续传的文件
     * 文件必须支持断点续传(uploadAppendable命令创建的文件)
     *
     * @param string $path
     * @param string $filename
     * @param int $offset
     * @return Response
     */
    public function modifyWithFile(string $path, string $filename, int $offset = 0)
    {
        $fileInfo = new SplFileInfo($filename);

        if (!$fileInfo->isFile() || !$fileInfo->isReadable()) {
            throw new InvalidArgumentException('文件不存在或不可读');
        }

        $stream = new Stream(fopen($fileInfo->getPathname(), 'r'));

        return $this->callCommand('modify', [$offset, $path, $stream]);
    }

    /**
     * 修改支持断点续传的文件
     * 文件必须支持断点续传(uploadAppendable命令创建的文件)
     *
     * @param string $path
     * @param string $buffer
     * @param int $offset
     * @return Response
     */
    public function modifyWithBuffer(string $path, string $buffer, int $offset = 0)
    {
        $resource = fopen('php://temp', 'w+');

        fwrite($resource, $buffer);

        return $this->modifyWithStream($path, $resource, $offset);
    }

    /**
     * 根据流修改支持断点续传的文件
     * 文件必须支持断点续传(uploadAppendable命令创建的文件)
     *
     * @param string $path
     * @param resource $resource
     * @param int $offset
     * @return Response
     */
    public function modifyWithStream(string $path, $resource, int $offset = 0)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('数据流不可用');
        }

        rewind($resource);

        return $this->callCommand('modify', [$offset, $path, new Stream($resource)]);
    }

    /**
     * 截取文件,超出长度的部分会被抛弃
     * 文件必须支持断点续传(uploadAppendable命令创建的文件)
     * 
     * @param string $path
     * @param int $fileSize
     * @return Response
     */
    public function truncate(string $path, int $fileSize): Response
    {
        return $this->callCommand('truncate', [$fileSize, $path]);
    }

    /**
     * 下载文件内容
     *
     * @param string $path
     * @param int $offset
     * @param int $length
     * @return DownloadData
     */
    public function download(
        string $path, 
        int $offset = 0, 
        int $length = 0,
    ): DownloadData {
        return $this->callCommand('download', [$offset, $length, $this->storageNode->group, $path]);
    }

    /**
     * 查询文件信息
     * 
     * @param string $path
     * @return FileInfo
     */
    public function query(string $path): FileInfo
    {
        return $this->callCommand('query', [$this->storageNode->group, $path]);
    }

    /**
     * 删除文件
     * 
     * @param string $path
     * @return Response
     */
    public function delete(string $path): Response
    {
        return $this->callCommand('delete', [$this->storageNode->group, $path]);
    }

    /**
     * 新增或覆盖文件元数据
     *
     * @param string $path
     * @return Response
     */
    public function mergeMetadata(string $path, array $metadata)
    {
        return $this->callCommand('setMetadata', [
            OperationFlag::MERGE, $this->storageNode->group, $path, $metadata
        ]);
    }

    /**
     * 覆写文件元数据
     *
     * @param string $path
     * @return Response
     */
    public function overwriteMetadata(string $path, array $metadata)
    {
        return $this->callCommand('setMetadata', [
            OperationFlag::OVERWRITE, $this->storageNode->group, $path, $metadata
        ]);
    }

    /**
     * 获取文件元数据
     *
     * @param string $path
     * @return FileMetadataSet
     */
    public function getMetadata(string $path): FileMetadataSet
    {
        return $this->callCommand('getMetadata', [$this->storageNode->group, $path]);
    }
}
