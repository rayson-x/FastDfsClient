<?php

namespace Ant\FastDFS\Commands\Tracker;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\Struct\StorageNode;

/**
 * 从Tracker服务器获取可用的Storage服务器
 *
 * @package Ant\FastDFS\Commands\Tracker
 */
class GetStoreStorage extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 101;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new StorageNode($this->mapper);
    }
}
