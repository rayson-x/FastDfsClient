<?php

namespace Ant\FastDFS\Commands\Storage;

use InvalidArgumentException;
use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Contracts\Stream;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\Response\StorePath;

/**
 * 上传文件
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class Upload extends Command
{
    /**
     * 储存节点index
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_BYTE, index: 0)]
    protected $storeIndex;

    /**
     * 文件大小
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    protected $fileSize;

    /**
     * 文件扩展名
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::FILE_EXT_NAME_MAX_SIZE ,index: 2)]
    protected $fileExtName;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 11;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(): Response
    {
        return new StorePath($this->mapper);
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

        [$storeIndex, $fileStream, $extension] = $arguments;

        if (!$fileStream instanceof Stream) {
            throw new InvalidArgumentException('Argument #2 must be of type Ant\FastDFS\Contracts\Stream');
        }

        $this->inputStream = $fileStream;

        parent::setArguments([$storeIndex, $fileStream->getSize(), $extension]);
    }
}
