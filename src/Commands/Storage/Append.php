<?php

namespace Ant\FastDFS\Commands\Storage;

use InvalidArgumentException;
use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Contracts\Stream;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * 文件断点续传(追加文件内容)
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class Append extends Command
{
    /**
     * 路径长度
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 0)]
    protected $pathSize;

    /**
     * 文件大小
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    protected $fileSize;

    /**
     * 追加文件名
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
        return 24;
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

        [$path, $fileStream] = $arguments;

        if (!$fileStream instanceof Stream) {
            throw new InvalidArgumentException('Argument #2 must be of type Ant\FastDFS\Contracts\Stream');
        }

        $this->inputStream = $fileStream;

        parent::setArguments([strlen($path), $fileStream->getSize(), $path]);
    }
}