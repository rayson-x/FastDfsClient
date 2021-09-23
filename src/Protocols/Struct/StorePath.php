<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class StorePath extends Response
{
    /**
     * 组名
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::GROUP_NAME_SIZE, index: 0)]
    public $group;

    /**
     * 存储路径
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_ALL_REST_BYTE, index: 1)]
    public $path;
}