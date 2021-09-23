<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class StorageNodeInfo extends Response
{
    /**
     * 组名
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 0, max: Common::GROUP_NAME_SIZE)]
    public $group;

    /**
     * 存储服务器ip地址
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 1, max: Common::IP_ADRESS_SIZE - 1)]
    public $ip;

    /**
     * 存储服务器端口
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 2)]
    public $port;
}