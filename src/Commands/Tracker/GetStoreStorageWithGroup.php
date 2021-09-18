<?php

namespace Ant\FastDFS\Commands\Tracker;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\Struct\StorageNode;

/**
 * 根据分组名称从Tracker服务器获取可用的Storage服务器
 *
 * @package Ant\FastDFS\Commands\Tracker
 */
class GetStoreStorageWithGroup extends Command
{
    /**
     * 分组名称
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::GROUP_NAME_SIZE)]
    protected $group;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 104;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new StorageNode($this->mapper);
    }
}
