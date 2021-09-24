<?php

namespace Ant\FastDFS\Commands\Storage;

use InvalidArgumentException;
use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * 截取文件
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class Truncate extends Command
{
    /**
     * 路径长度
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 0)]
    protected $pathSize;

    /**
     * 截取文件长度
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    protected $fileSize;

    /**
     * 文件路径
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_ALL_REST_BYTE ,index: 2)]
    protected $path;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 36;
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $arguments)
    {
        if (count($arguments) !== 2) {
            throw new InvalidArgumentException(sprintf(
                'expects at least 2 arguments, %d given', count($arguments)
            ));
        }

        [$fileSize, $path] = $arguments;

        parent::setArguments([strlen($path), $fileSize, $path]);
    }
}
