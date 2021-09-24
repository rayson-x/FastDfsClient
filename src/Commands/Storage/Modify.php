<?php

namespace Ant\FastDFS\Commands\Storage;

use InvalidArgumentException;
use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Contracts\Stream;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * 修改文件
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class Modify extends Command
{
    /**
     * 路径长度
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 0)]
    protected $pathSize;

    /**
     * 修改位置
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    protected $offset;

    /**
     * 文件大小
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 2)]
    protected $fileSize;

    /**
     * 追加文件名
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
        return 34;
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $arguments)
    {
        if (count($arguments) !== 3) {
            throw new InvalidArgumentException(sprintf(
                'expects at least 3 arguments, %d given', count($arguments)
            ));
        }

        [$offset, $path, $fileStream] = $arguments;

        if (!$fileStream instanceof Stream) {
            throw new InvalidArgumentException('Argument #3 must be of type Ant\FastDFS\Contracts\Stream');
        }

        $this->inputStream = $fileStream;

        parent::setArguments([strlen($path), $offset, $fileStream->getSize(), $path]);
    }
}
