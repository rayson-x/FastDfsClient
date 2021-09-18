<?php

namespace Ant\FastDFS\Protocols\Response;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Response
 */
class StorePath extends Response
{
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::GROUP_NAME_SIZE, index: 0)]
    public $group;

    #[FastDFSParam(type: FastDFSParam::TYPE_ALL_REST_BYTE, index: 1)]
    public $path;
}