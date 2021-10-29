<?php

namespace Tests\Ant\FastDFS\Connections\Default;

use Ant\FastDFS\Exceptions\ServerException;
use Ant\FastDFS\Protocols\Struct\StorageNode;
use Tests\Ant\FastDFS\Commands\CommandTestCase;
use Ant\FastDFS\Commands\Tracker\GetStoreStorageWithGroup;

class GetStoreStorageWithGroupTest extends CommandTestCase
{
    /**
     * @return string
     */
    public function getCommandClass()
    {
        return GetStoreStorageWithGroup::class;
    }

    /**
     * @return string
     */
    public function getExpectedCmd()
    {
        return 104;
    }

    /**
     * @group connected
     */
    public function testGetStoreStorageWithGroup()
    {
        $client   = $this->getTrackerClient();
        $response = $client->getStoreStorageWithGroup($this->getGroupName());

        $this->assertInstanceOf(StorageNode::class, $response);
    }

    /**
     * @group connected
     */
    public function testGetStoreStorageWithInvalidGroup()
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('错误码: 2, 错误信息: 文件或目录不存在');

        $client = $this->getTrackerClient();
        $client->getStoreStorageWithGroup($this->getGroupName(invalid: true));
    }
}
