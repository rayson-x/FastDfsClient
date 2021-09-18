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
    protected Head $head;

    /**
     * @param ObjectMetadata $meta
     * @param Command $command
     * @param Stream|null $inputFileStream
     */
    public function __construct(
        protected ObjectMetadata $meta, 
        protected Command $command,
        protected ?Stream $inputFileStream = null
    ) {
        $size = $meta->getFieldsSendTotalSize($command);

        if ($inputFileStream !== null) {
            $size += $inputFileStream->getSize();
        }

        $this->head = new Head($size, $command->getCmd());
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
