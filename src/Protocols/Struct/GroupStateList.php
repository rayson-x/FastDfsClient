<?php

namespace Ant\FastDFS\Protocols\Struct;

use ArrayIterator;
use IteratorAggregate;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Exceptions\IOException;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class GroupStateList extends Response implements IteratorAggregate
{
    /**
     * 可用的储存组
     * 
     * @var array<GroupState>
     */
    public array $groups = [];

    /**
     * {@inheritdoc}
     */
    public function decode(string $buffer)
    {
        $meta   = $this->mapper->getObjectMetadata(GroupState::class);
        $size   = $meta->getFieldTotalSize();
        $length = strlen($buffer);

        // 检查响应长度是否正确
        if ($length % $size !== 0) {
            throw new IOException("buffer length: {$length} is invalid!");
        }

        // 计算分组数量
        $count  = $length / $size;
        $offset = 0;

        for ($i = 0; $i < $count; $i++) {
            $this->groups[] = $meta->newInstance(substr($buffer, $offset, $size));

            $offset += $size;
        }
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->groups);
    }
}
