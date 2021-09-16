<?php

namespace Ant\FastDFS\Protocols;

use ReflectionClass;
use InvalidArgumentException;

/**
 * 生成对象上传输的参数信息,并负责encode与decode
 *
 * @package Ant\FastDFS\Protocols
 */
class ObjectMetadata
{
    /**
     * @var ReflectionClass
     */
    protected ReflectionClass $reflect;

    /**
     * 传输时使用的字段
     *
     * @var array<FieldMetadata>
     */
    protected array $fields = [];

    /**
     * 传输时内容大小不固定的列
     *
     * @var array<FieldMetadata>
     */
    protected array $dynamicFields = [];

    /**
     * 字段总长度(不包含动态长度的字段)
     *
     * @var int
     */
    protected int $fieldTotalSize = 0;

    /**
     * @param string $class
     */
    public function __construct(protected string $class)
    {
        $this->reflect = new ReflectionClass($this->class);

        $this->parseFields();
    }

    /**
     * 解析对象要传输的字段
     */
    protected function parseFields()
    {
        foreach ($this->reflect->getProperties() as $property) {
            $attributes = $property->getAttributes(FastDFSParam::class);

            if (empty($attributes)) {
                continue;
            }

            $field = new FieldMetadata($property, $attributes[0], $this->fieldTotalSize);

            if (isset($this->fields[$field->getIndex()])) {
                $otherField = $this->fields[$field->getIndex()];

                throw new InvalidArgumentException(
                    "类{$this->class}中{$property->getName()}与{$otherField->getName()}索引定义相同"
                );
            }

            $this->fields[$field->getIndex()] = $field;

            $this->fieldTotalSize += $field->getSize();

            if ($field->isDynamicField()) {
                array_push($this->dynamicFields, $field);
            }
        }
    }

    /**
     * 获取类名
     * 
     * @return string
     */
    public function getClassName(): string
    {
        return $this->class;
    }

    /**
     * 获取需要的参数
     *
     * @return array<FieldMetadata>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * 获取字段总长度
     *
     * @return int
     */
    public function getFieldTotalSize(): int
    {
        return $this->fieldTotalSize;
    }

    /**
     * 获取字段总长度,包含动态字段长度
     *
     * @return int
     */
    public function getDynamicTotalFieldSize(object $bean): int
    {
        $size = 0;
        foreach ($this->dynamicFields as $field) {
            $size = $field->getDynamicSize($bean);
        }

        return $size;
    }

    /**
     * 获取要发送内容的字段总长
     *
     * @return int
     */
    public function getFieldsSendTotalSize(object $bean): int
    {
        $size = $this->fieldTotalSize;

        if (!empty($this->dynamicFields)) {
            $size += $this->getDynamicTotalFieldSize($bean);
        }

        return $size;
    }

    /**
     * 转换为byte
     *
     * @param object $bean
     * @return string
     */
    public function toByte(object $bean): string
    {
        $result = '';
        foreach ($this->getFields() as $field) {
            $result .= $field->toByte($bean);
        }

        return $result;
    }

    /**
     * 从byte转换为对象
     *
     * @param string $byte
     * @return string
     */
    public function newInstance(string $byte): object
    {
        $object = $this->reflect->newInstance();
        foreach ($this->getFields() as $field) {
            $field->getProperty()->setValue($object, $field->getValue($byte));
        }

        return $object;
    }
}
