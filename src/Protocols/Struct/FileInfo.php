<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class FileInfo extends Response
{
    /**
     * 文件大小
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 0)]
    public $size;

    /**
     * 创建时间
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    public $createAt;

    /**
     * 校验码
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 2)]
    public $crc32;

    /**
     * ip地址
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 3, max: Common::IP_ADRESS_SIZE)]
    public $ip;
}