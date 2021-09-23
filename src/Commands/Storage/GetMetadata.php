<?php

namespace Ant\FastDFS\Commands\Storage;

use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\Struct\FileMetadataSet;

/**
 * 获取文件元数据
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class GetMetadata extends Command
{
    /**
     * 分组名称
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::GROUP_NAME_SIZE, index: 0)]
    protected $group;

    /**
     * 文件路径
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_ALL_REST_BYTE, index: 1)]
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 15;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new FileMetadataSet($this->mapper);
    }
}
