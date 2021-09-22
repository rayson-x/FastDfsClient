<?php

namespace Ant\FastDFS\Protocols;

use ReflectionProperty;
use ReflectionAttribute;
use Ant\FastDFS\BytesUtil;
use InvalidArgumentException;
use Ant\FastDFS\Constants\Common;

/**
 * FieldMetadata
 *
 * @package Ant\FastDFS\Protocols
 */
class FieldMetadata
{
    /**
     * @return int
     */
    protected int $type;

    /**
     * @return int
     */
    protected int $index = 0;

    /**
     * @return int
     */
    protected int $max = 0;

    /**
     * @return int|null
     */
    protected ?int $size = null;

    /**
     * @param ReflectionProperty $property
     * @param ReflectionAttribute $attribute
     * @param int $offset 字段偏移量,因为没有分隔符,需要通过偏移量计算字段
     */
    public function __construct(
        protected ReflectionProperty $property,
        protected ReflectionAttribute $attribute,
        protected int $offset,
    ) {
        $property->setAccessible(true);

        foreach ($attribute->getArguments() as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * 获取字段名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->property->getName();
    }

    /**
     * 获取字段顺序
     *
     * @return int
     */
    public function getIndex() : int
    {
        return $this->index;
    }

    /**
     * 获取字段最大可用长度
     *
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * 获取字段偏移量
     *
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * 获取字段类型
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * 获取属性
     * 
     * @return ReflectionProperty
     */
    public function getProperty(): ReflectionProperty
    {
        return $this->property;
    }

    /**
     * 获取字段长度
     *
     * @return int
     * @throws InvalidArgumentException
     */
    public function getSize(): int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        $this->size = match ($this->type) {
            FastDFSParam::TYPE_STRING => $this->max,
            FastDFSParam::TYPE_INT    => Common::LONG_SIZE,
            FastDFSParam::TYPE_BYTE   => Common::BYTE_SIZE,
            FastDFSParam::TYPE_BOOL   => Common::BYTE_SIZE,
            default                   => 0,
        };

        // 强制设置了最大值,就以最大值为准
        if ($this->max > 0 && $this->size > $this->max) {
            $this->size = $this->max;
        }

        return $this->size;
    }

    /**
     * 是否为动态长度的字段
     * 
     * @return bool
     */
    public function isDynamicField(): bool
    {
        return FastDFSParam::isDynamicField($this->type);
    }

    /**
     * 转换为byte
     *
     * @param object $bean
     * @return string
     */
    public function toByte(object $bean): string
    {
        $value = $this->property->getValue($bean);

        return match ($this->type) {
            FastDFSParam::TYPE_STRING        => BytesUtil::padding($value, $this->max),
            FastDFSParam::TYPE_INT           => BytesUtil::long2buff($value),
            FastDFSParam::TYPE_BYTE          => BytesUtil::padding($value, Common::BYTE_SIZE),
            FastDFSParam::TYPE_NULLABLE      => BytesUtil::padding($value, $this->max),
            FastDFSParam::TYPE_FILE_META     => '', // TODO FileMeta
            FastDFSParam::TYPE_ALL_REST_BYTE => $value,
            default                          => throw new InvalidArgumentException("类型错误无法转换为byte"),
        };
    }

    /**
     * 从byte转换为可用的类型
     * 
     * @param string $byte
     * @return mixed
     */
    public function getValue(string $byte): mixed
    {
        if ($this->type === FastDFSParam::TYPE_ALL_REST_BYTE) {
            return trim(substr($byte, $this->offset));
        } elseif ($this->type === FastDFSParam::TYPE_FILE_META) {
            // TODO FileMeta
            return '';
        }

        $value = substr($byte, $this->offset, $this->getSize());

        if ($this->type === FastDFSParam::TYPE_INT) {
            $value = BytesUtil::buff2long($value);
        } elseif ($this->type === FastDFSParam::TYPE_STRING) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * 获取字段动态大小
     *
     * @param object $bean
     * @return int
     */
    public function getDynamicSize(object $bean): int
    {
        $value = $this->property->getValue($bean);

        if (empty($value)) {
            return 0;
        }

        if ($this->type === FastDFSParam::TYPE_ALL_REST_BYTE) {
            return strlen($value);
        } elseif ($this->type === FastDFSParam::TYPE_FILE_META) {
            // TODO FileMeta
            return 0;
        } else {
            return $this->getSize();
        }
    }
}
