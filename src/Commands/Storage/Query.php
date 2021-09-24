<?php

namespace Ant\FastDFS\Commands\Storage;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\Struct\FileInfo;

/**
 * 查询文件信息
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class Query extends Command
{
    /**
     * 组名
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 0, max: Common::GROUP_NAME_SIZE)]
    protected $group;

    /**
     * 路径名
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_ALL_REST_BYTE ,index: 1)]
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 22;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new FileInfo($this->mapper);
    }
}
