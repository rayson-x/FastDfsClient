<?php

namespace Ant\FastDFS\Protocols;

use Ant\FastDFS\Contracts\Response as ResponseContract;

/**
 * FastDFS Response
 *
 * @package Ant\FastDFS\Protocols
 */
class Response implements ResponseContract
{
    /**
     * @var MetadataMapper
     */
    protected $mapper;

    /**
     * @param MetadataMapper $mapper
     */
    public function __construct(MetadataMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function decode(string $buffer)
    {
        $meta = $this->mapper->getObjectMetadata($this);

        foreach ($meta->getFields() as $field) {
            $this->{$field->getName()} = $field->getValue($buffer);
        }
    }
}
