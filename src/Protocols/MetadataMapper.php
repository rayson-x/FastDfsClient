<?php

namespace Ant\FastDFS\Protocols;

/**
 * MetadataMapper
 *
 * @package Ant\FastDFS\Protocols
 */
class MetadataMapper
{
    /**
     * 元数据缓存
     * 
     * @param array
     */
    protected $mapCache = [];

    /**
     * 获取对象元数据
     * 
     * @param string $class
     * @return ObjectMetadata
     */
    public function getObjectMetadata(string|object $class): ObjectMetadata
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!empty($this->mapCache[$class])) {
            return $this->mapCache[$class];
        }

        return $this->mapCache[$class] = new ObjectMetadata($class);
    }
}
