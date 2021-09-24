<?php

namespace Ant\FastDFS\Commands\Storage;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\Struct\DownloadData;

/**
 * 下载文件
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class Download extends Command
{
    /**
     * 开始位置
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 0)]
    protected $offset;

    /**
     * 读取长度
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    protected $length;

    /**
     * 组名
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, index: 2, max: Common::GROUP_NAME_SIZE)]
    protected $group;

    /**
     * 路径名
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_ALL_REST_BYTE ,index: 3)]
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 14;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new DownloadData($this->mapper);
    }
}
