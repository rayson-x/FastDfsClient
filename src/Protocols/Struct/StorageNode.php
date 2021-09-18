<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class StorageNode extends Response
{
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::GROUP_NAME_SIZE, index: 0)]
    public $group;

    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::IP_ADRESS_SIZE - 1, index: 1)]
    public $ip;

    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 2)]
    public $port;

    #[FastDFSParam(type: FastDFSParam::TYPE_BYTE, index: 3)]
    public $storeIndex;
}