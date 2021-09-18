<?php

namespace Ant\FastDFS\Commands;

use InvalidArgumentException;
use Ant\FastDFS\Contracts\Stream;
use Ant\FastDFS\Protocols\Request;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\MetadataMapper;
use Ant\FastDFS\Contracts\Command as CommandContract;
use Ant\FastDFS\Contracts\Request as RequestContract;
use Ant\FastDFS\Contracts\Response as ResponseContract;

/**
 * @package Ant\FastDFS\Commands
 */
abstract class Command implements CommandContract
{
    /**
     * @var MetadataMapper
     */
    protected MetadataMapper $mapper;

    /**
     * @var Stream|null
     */
    protected ?Stream $inputStream = null;

    /**
     * @param MetadataMapper $mapper
     */
    public function __construct(MetadataMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 设置指令使用的参数.
     *
     * @param array.
     */
    public function setArguments(array $arguments)
    {
        $meta = $this->mapper->getObjectMetadata($this);

        foreach ($meta->getFields() as $field) {
            if (
                !array_key_exists($field->getIndex(), $arguments) &&
                $field->getType() !== FastDFSParam::TYPE_NULLABLE
            ) {
                throw new InvalidArgumentException(
                    "{$field->getName()} is not allowed to be empty"
                );
            }

            $this->{$field->getName()} = $arguments[$field->getIndex()] ?? '';
        }
    }

    /**
     * @return RequestContract
     */
    public function getRequest(): RequestContract
    {
        $meta = $this->mapper->getObjectMetadata($this);

        return new Request($meta, $this, $this->inputStream);
    }

    /**
     * 返回可以解析Response对象
     *
     * @return Response
     */
    public function getResponse(): ResponseContract
    {
        return new Response($this->mapper);
    }
}
