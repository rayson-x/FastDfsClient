<?php

namespace Tests\Ant\FastDFS\Commands;

use Ant\FastDFS\TrackerClient;
use PHPUnit\Framework\TestCase;
use Ant\FastDFS\Contracts\Command;
use Ant\FastDFS\Connections\Connector;
use Ant\FastDFS\Protocols\MetadataMapper;

abstract class CommandTestCase extends TestCase
{
    /**
     * @return string
     */
    abstract protected function getCommandClass();

    /**
     * @return int
     */
    abstract protected function getExpectedCmd();

    /**
     * @return Command
     */
    public function getCommand()
    {
        $command = $this->getCommandClass();

        return new $command(new MetadataMapper);
    }

    /**
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [
            'host'    => constant('TRACKER_SERVER_HOST'),
            'port'    => constant('TRACKER_SERVER_PORT'),
            'driver'  => constant('CONNECTION_DRIVER'),
            'timeout' => 2,
        ];
    }

    /**
     * @param array $config
     * @return \Ant\FastDFS\TrackerClient
     */
    public function getTrackerClient(array $config = [])
    {
        $config = $config ?: $this->getDefaultConfig();

        $uri = "{$config['host']}:{$config['port']}";

        return new TrackerClient([$uri], new Connector($config['driver']));
    }

    /**
     * @param array $config
     * @return \Ant\FastDFS\StorageClient
     */
    public function getStorageClient(array $config = [])
    {
        $trackerClient = $this->getTrackerClient($config);

        return $trackerClient->getStorageClient();
    }

    /**
     * 获取组名,根据invalid来判断生成有效组名还是无效
     * 
     * @param bool $invalid
     * @return string
     */
    protected function getGroupName($invalid = false)
    {
        $names = [];
        foreach ($this->getTrackerClient()->getGroups()->groups as $group) {
            if (!$invalid) {
                return $group->name;
            } else {
                $names[$group->name] = true;
            }
        }

        while (true) {
            $group = 'test_group_'.rand(1000, 9999);

            if (empty($names[$group])) {
                return $group;
            }
        }
    }

    /**
     * @group disconnected
     */
    public function testCommand(): void
    {
        $command = $this->getCommand();

        $this->assertInstanceOf(Command::class, $command);
        $this->assertEquals($this->getExpectedCmd(), $command->getCmd());
    }
}
