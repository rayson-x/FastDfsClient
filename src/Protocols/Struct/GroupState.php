<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class GroupState extends Response
{
    /**
     * name of this group
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::GROUP_NAME_SIZE + 1, index: 0)]
    public $name;

    /**
     * total disk storage in MB
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    public $totalMB;

    /**
     * free disk space in MB
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 2)]
    public $freeMB;

    /**
     * trunk free space in MB
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 3)]
    public $trunkFreeMB;

    /**
     * storage server count
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 4)]
    public $storageCount;

    /**
     * storage server port
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 5)]
    public $storagePort;

    /**
     * storage server HTTP port
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 6)]
    public $storageHttpPort;

    /**
     * active storage server count
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 7)]
    public $activeCount;

    /**
     * current storage server index to upload file
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 8)]
    public $currentWriteServer;

    /**
     * store base path count of each storage server
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 9)]
    public $storePathCount;

    /**
     * sub dir count per store path
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 10)]
    public $subdirCountPerPath;

    /**
     * current trunk file id
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 11)]
    public $currentTrunkFileId;
}
