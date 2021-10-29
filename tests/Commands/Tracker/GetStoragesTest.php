<?php

namespace Tests\Ant\FastDFS\Connections\Default;

use Ant\FastDFS\Exceptions\IOException;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Exceptions\ServerException;
use Ant\FastDFS\Commands\Tracker\GetStorages;
use Ant\FastDFS\Protocols\Struct\StorageState;
use Tests\Ant\FastDFS\Commands\CommandTestCase;
use Ant\FastDFS\Protocols\Struct\StorageStateList;

class GetStoragesTest extends CommandTestCase
{
    /**
     * @return string
     */
    public function getCommandClass()
    {
        return GetStorages::class;
    }

    /**
     * @return string
     */
    public function getExpectedCmd()
    {
        return 92;
    }

    /**
     * @group connected
     */
    public function testGetStorages()
    {
        $client   = $this->getTrackerClient();
        $response = $client->getStorages($this->getGroupName());

        $this->assertInstanceOf(StorageStateList::class, $response);
        $this->assertInstanceOf(StorageState::class, $response->storages[0]);
    }

    /**
     * @group connected
     */
    public function testGetStoragesWithInvalidGroup()
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage("错误码: 2, 错误信息: 文件或目录不存在");

        $client = $this->getTrackerClient();
        $client->getStorages($this->getGroupName(invalid: true));
    }

    /**
     * @group connected
     */
    public function testGetStoragesWithInvalidIpAddress()
    {
        $this->expectException(ServerException::class);
        $this->expectExceptionMessage("错误码: 2, 错误信息: 文件或目录不存在");

        $client = $this->getTrackerClient();
        $client->getStorages($this->getGroupName(), '8.8.8.8');
    }

    /**
     * @group disconnected
     */
    public function testDecodeInvalidBuffer()
    {
        $mapper = new MetadataMapper();
        $meta   = $mapper->getObjectMetadata(StorageState::class);
        $size   = $meta->getFieldTotalSize();
        $buffer = str_pad('', $size - 1, ' ');
        $length = strlen($buffer);

        $this->expectException(IOException::class);
        $this->expectExceptionMessage("buffer length: {$length} is invalid!");

        $command  = $this->getCommand();
        $response = $command->getResponse();
        $response->decode($buffer);
    }
}
