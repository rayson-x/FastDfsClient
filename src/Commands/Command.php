<?php

namespace Ant\FastDFS\Commands;

use InvalidArgumentException;
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
     * @var string
     */
    protected string $response;

    /**
     * @var MetadataMapper
     */
    protected MetadataMapper $mapper;

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

        // TODO 单独封装一个方法,把Stream类型的字段进行转换
        // 检查是否为文件->FileStream,普通字符串->BufferStream
        foreach ($meta->getFields() as $field) {
            if (
                empty($arguments[$field->getIndex()]) &&
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
        return new Request($this->mapper->getObjectMetadata($this), $this);
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
