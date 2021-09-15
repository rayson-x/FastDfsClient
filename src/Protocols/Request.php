<?php

namespace Ant\FastDFS\Protocols;

use Ant\FastDFS\Contracts\Stream;
use Ant\FastDFS\Contracts\Command;
use Ant\FastDFS\Contracts\Request as RequestContract;

/**
 * FastDFS Request
 *
 * @package Ant\FastDFS\Protocols
 */
class Request implements RequestContract
{
    /**
     * 报文头
     *
     * @var Head
     */
    protected $head;

    /**
     * 文件的stream,默认为空,子类继承后设置该值
     * 
     * @var Stream|null
     */
    protected $inputFileStream = null;

    /**
     * @param ObjectMetadata $meta
     * @param Command $command
     */
    public function __construct(protected ObjectMetadata $meta, protected Command $command)
    {
        $this->head = new Head(
            $meta->getFieldsSendTotalSize($command), $command->getCmd(), 0
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadByte(): string
    {
        return $this->head->toBytes();
    }

    /**
     * {@inheritdoc}
     */
    public function getParamByte(): string
    {
        return $this->meta->toByte($this->command);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFileStream(): ?Stream
    {
        return $this->inputFileStream;
    }
}
