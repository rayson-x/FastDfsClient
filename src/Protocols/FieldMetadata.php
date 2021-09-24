<?php

namespace Ant\FastDFS\Protocols;

use ReflectionProperty;
use Ant\FastDFS\BytesUtil;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Exceptions\ProtocolException;

/**
 * FieldMetadata
 *
 * @package Ant\FastDFS\Protocols
 */
class FieldMetadata
{
    /**
     * @return int|null
     */
    protected ?int $size = null;

    /**
     * @param ReflectionProperty $property
     * @param FastDFSParam $param
     * @param int $offset 字段偏移量,因为没有分隔符,需要通过偏移量计算字段
     */
    public function __construct(
        protected ReflectionProperty $property,
        protected FastDFSParam $param,
        protected int $offset,
    ) {
        $property->setAccessible(true);
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
     * 获取字段类型
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->param->type;
    }

    /**
     * 获取字段顺序
     *
     * @return int
     */
    public function getIndex() : int
    {
        return $this->param->index;
    }

    /**
     * 获取字段最大可用长度
     *
     * @return int
     */
    public function getMax(): int
    {
        return $this->param->max;
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
     */
    public function getSize(): int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        $this->size = match ($this->param->type) {
            FastDFSParam::TYPE_STRING => $this->param->max,
            FastDFSParam::TYPE_INT    => Common::LONG_SIZE,
            FastDFSParam::TYPE_BYTE   => Common::BYTE_SIZE,
            FastDFSParam::TYPE_BOOL   => Common::BYTE_SIZE,
            default                   => 0,
        };

        // 强制设置了最大值,就以最大值为准
        if ($this->param->max > 0 && $this->size > $this->param->max) {
            $this->size = $this->param->max;
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
        return FastDFSParam::isDynamicField($this->param->type);
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

        return match ($this->param->type) {
            FastDFSParam::TYPE_STRING        => BytesUtil::padding($value, $this->param->max),
            FastDFSParam::TYPE_INT           => BytesUtil::long2buff($value),
            FastDFSParam::TYPE_BYTE          => BytesUtil::padding($value, Common::BYTE_SIZE),
            FastDFSParam::TYPE_BOOL          => $value ? hex2bin('01') : hex2bin('00'),
            FastDFSParam::TYPE_NULLABLE      => !empty($value) ? BytesUtil::padding($value, $this->param->max) : '',
            FastDFSParam::TYPE_FILE_META     => $value,
            FastDFSParam::TYPE_ALL_REST_BYTE => $value,
            default                          => throw new ProtocolException('Type errors cannot be converted to bytes'),
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
        if ($this->param->type === FastDFSParam::TYPE_ALL_REST_BYTE) {
            return trim(substr($byte, $this->offset));
        } elseif ($this->param->type === FastDFSParam::TYPE_FILE_META) {
            $fieldSeperator = hex2bin(Common::FIELD_SEPERATOR);
            $lineSeperator  = hex2bin(Common::LINE_SEPERATOR);
    
            $metadata = [];
            foreach (explode($lineSeperator, $byte) as $info) {
                [$key, $value] = explode($fieldSeperator, $info);

                $metadata[$key] = $value;
            }

            return $metadata;
        } elseif ($this->param->type === FastDFSParam::TYPE_NULLABLE) {
            throw new ProtocolException('Nullable does not support conversion');
        }

        $value = substr($byte, $this->offset, $this->getSize());

        return match ($this->param->type) {
            FastDFSParam::TYPE_INT    => BytesUtil::buff2long($value),
            FastDFSParam::TYPE_BOOL   => bin2hex($value) == '01',
            FastDFSParam::TYPE_STRING => trim($value),
            default                   => $value,
        };
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

        // 值为空时不计算长度
        if (empty($value)) {
            return 0;
        }

        switch ($this->param->type) {
            case FastDFSParam::TYPE_ALL_REST_BYTE:
            case FastDFSParam::TYPE_FILE_META:
                return strlen($value);
            case FastDFSParam::TYPE_NULLABLE:
                return $this->param->max > 0 ? $this->param->max : strlen($value);
            default:
                return $this->getSize();
        }
    }
}
