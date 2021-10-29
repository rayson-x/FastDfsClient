<?php

namespace Tests\Ant\FastDFS\Connections\Default;

use Ant\FastDFS\Exceptions\ServerException;
use Tests\Ant\FastDFS\Commands\CommandTestCase;
use Ant\FastDFS\Commands\Tracker\GetFetchStorage;
use Ant\FastDFS\Protocols\Struct\StorageNodeInfo;

class GetFetchStorageTest extends CommandTestCase
{
    /**
     * @return string
     */
    public function getCommandClass()
    {
        return GetFetchStorage::class;
    }

    /**
     * @return string
     */
    public function getExpectedCmd()
    {
        return 102;
    }

    /**
     * @group connected
     */
    public function testGetFetchStorage()
    {
        $trackerClient = $this->getTrackerClient();
        $storageClient = $this->getStorageClient();

        $info     = $storageClient->uploadBuffer('foobar');
        $response = $trackerClient->getFetchStorage($info->group, $info->path);

        $this->assertInstanceOf(StorageNodeInfo::class, $response);

        $storageClient->delete($info->path);
    }

    /**
     * @group connected
     */
    public function testGetFetchStorageWithInvalidGroup()
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage("错误码: 2, 错误信息: 文件或目录不存在");

        $trackerClient = $this->getTrackerClient();
        $storageClient = $this->getStorageClient();

        $info = $storageClient->uploadBuffer('foobar');

        $storageClient->delete($info->path);

        $trackerClient->getFetchStorage($this->getGroupName(invalid: true), $info->path);
    }

    /**
     * @group connected
     */
    public function testGetFetchStorageWithInvalidPath()
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage("错误码: 22, 错误信息: 无效的参数");

        $trackerClient = $this->getTrackerClient();
        $trackerClient->getFetchStorage($this->getGroupName(), 'foobar');
    }
}
