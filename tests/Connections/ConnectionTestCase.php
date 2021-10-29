<?php

namespace Tests\Ant\FastDFS\Connections;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Ant\FastDFS\Contracts\Stream;
use Ant\FastDFS\Contracts\Connection;

abstract class ConnectionTestCase extends TestCase
{
    /**
     * @group disconnected
     */
    public function testInvalidConnection()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Connection to 127.0.0.2:22122 failed: Operation timed out');

        $this->createConnection(['127.0.0.2', '22122', 0.1]);
    }

    /**
     * @group connected
     */
    public function testGetStream()
    {
        $connection = $this->createConnection();

        $this->assertInstanceOf(Stream::class, $connection->getStream());
    }

    /**
     * @group connected
     */
    public function testDisconnectForcesDisconnection()
    {
        $connection = $this->createConnection();

        $this->assertTrue($connection->isValid());

        $connection->close();

        $this->assertFalse($connection->isValid());
    }

    /**
     * @group connected
     */
    public function testDoesNotThrowExceptionOnCloseStreamWhenAlreadyDisconnected()
    {
        $connection = $this->createConnection();

        $connection->getStream()->close();

        $this->assertFalse($connection->isValid());

        $connection->close();

        $this->assertFalse($connection->isValid());
    }

    /**
     * @return string
     */
    abstract protected function getConnectionClass();

    /**
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [
            'host'    => constant('TRACKER_SERVER_HOST'),
            'port'    => constant('TRACKER_SERVER_PORT'),
            'timeout' => 2,
        ];
    }

    /**
     * @return Connection
     */
    protected function createConnection(array $params = [])
    {
        $class = $this->getConnectionClass();

        if (empty($params)) {
            $params = $this->getDefaultConfig();
        }

        return new $class(...array_values($params));
    }
}
