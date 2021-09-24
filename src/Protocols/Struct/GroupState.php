<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class GroupState
{
    /**
     * name of this group
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 0, max: Common::GROUP_NAME_SIZE + 1)]
    public $name;

    /**
     * total disk storage in MB
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    public $totalMB;

    /**
     * free disk space in MB
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 2)]
    public $freeMB;

    /**
     * trunk free space in MB
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 3)]
    public $trunkFreeMB;

    /**
     * storage server count
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 4)]
    public $storageCount;

    /**
     * storage server port
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 5)]
    public $storagePort;

    /**
     * storage server HTTP port
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 6)]
    public $storageHttpPort;

    /**
     * active storage server count
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 7)]
    public $activeCount;

    /**
     * current storage server index to upload file
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 8)]
    public $currentWriteServer;

    /**
     * store base path count of each storage server
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 9)]
    public $storePathCount;

    /**
     * sub dir count per store path
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 10)]
    public $subdirCountPerPath;

    /**
     * current trunk file id
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 11)]
    public $currentTrunkFileId;
}
