<?php

namespace Ant\FastDFS\Commands\Tracker;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\Struct\GroupStateList;

/**
 * 获取可用的分组
 *
 * @package Ant\FastDFS\Commands\Tracker
 */
class GetGroups extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 91;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new GroupStateList($this->mapper);
    }
}
