<?php

namespace Tests\Ant\FastDFS\Connections\Default;

use Ant\FastDFS\Protocols\Struct\StorageNode;
use Tests\Ant\FastDFS\Commands\CommandTestCase;
use Ant\FastDFS\Commands\Tracker\GetStoreStorage;

class GetStoreStorageTest extends CommandTestCase
{
    /**
     * @return string
     */
    public function getCommandClass()
    {
        return GetStoreStorage::class;
    }

    /**
     * @return string
     */
    public function getExpectedCmd()
    {
        return 101;
    }

    /**
     * @group connected
     */
    public function testGetStoreStorage()
    {
        $client = $this->getTrackerClient();

        $this->assertInstanceOf(StorageNode::class, $client->getStoreStorage());
    }
}
