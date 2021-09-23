<?php

namespace Ant\FastDFS\Commands\Tracker;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * 删除Storage服务器
 *
 * @package Ant\FastDFS\Commands\Tracker
 */
class DeleteStorage extends Command
{
    /**
     * 分组名称
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 0, max: Common::GROUP_NAME_SIZE)]
    protected $group;

    /**
     * 存储服务器ip
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 1, max: Common::IP_ADRESS_SIZE - 1)]
    protected $ip;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 93;
    }
}
