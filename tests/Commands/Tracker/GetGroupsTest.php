<?php

namespace Tests\Ant\FastDFS\Connections\Default;

use Ant\FastDFS\Commands\Tracker\GetGroups;
use Ant\FastDFS\Protocols\Struct\GroupState;
use Tests\Ant\FastDFS\Commands\CommandTestCase;
use Ant\FastDFS\Protocols\Struct\GroupStateList;

class GetGroupsTest extends CommandTestCase
{
    /**
     * @return string
     */
    public function getCommandClass()
    {
        return GetGroups::class;
    }

    /**
     * @return string
     */
    public function getExpectedCmd()
    {
        return 91;
    }

    /**
     * @group connected
     */
    public function testGetGroups()
    {
        $client   = $this->getTrackerClient();
        $response = $client->getGroups();

        $this->assertInstanceOf(GroupStateList::class, $response);
        $this->assertInstanceOf(GroupState::class, $response->groups[0]);
    }
}
