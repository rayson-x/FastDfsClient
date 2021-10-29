<?php

namespace Tests\Ant\FastDFS\Connections\Default;

use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Exceptions\IOException;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Connections\Default\Connection;
use Ant\FastDFS\Commands\Tracker\GetStoreStorage;
use Tests\Ant\FastDFS\Connections\ConnectionTestCase;

class ConnectionTest extends ConnectionTestCase
{
    protected function getConnectionClass()
    {
        return Connection::class;
    }

    /**
     * @group connected
     */
    public function testExecuteCommand()
    {
        $connection = $this->createConnection();

        $cmd = new GetStoreStorage(new MetadataMapper);

        $node = $connection->executeCommand($cmd);

        $this->assertInstanceOf(Response::class, $node);
    }

    /**
     * @group connected
     */
    public function testDisconnectForcesExecuteCommand()
    {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('Has been disconnected');

        $connection = $this->createConnection();

        $connection->close();
        $connection->executeCommand(new GetStoreStorage(new MetadataMapper));
    }
}
