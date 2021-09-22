<?php

namespace Ant\FastDFS\Commands\Tracker;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\Struct\StorageStateList;

/**
 * 获取储存节点信息
 *
 * @package Ant\FastDFS\Commands\Tracker
 */
class GetStorages extends Command
{
    /**
     * 组名
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 0, max: Common::GROUP_NAME_SIZE)]
    protected $groupName;

    /**
     * 存储服务器ip地址
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_NULLABLE, index: 1, max: Common::IP_ADRESS_SIZE - 1)]
    protected $storageIpAddr;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 92;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new StorageStateList($this->mapper);
    }
}
