<?php

namespace Ant\FastDFS\Commands\Storage;

use InvalidArgumentException;
use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Constants\OperationFlag;
use Ant\FastDFS\Protocols\FastDFSParam;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * 设置文件元数据
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class SetMetadata extends Command
{
    /**
     * 文件名长度
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 0)]
    protected $filenameLength;

    /**
     * 元数据长度
     * 
     * @var int
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    protected $metadataLength;

    /**
     * 操作标识(重写/覆盖)
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_BYTE, index: 2)]
    protected $operationFlag;

    /**
     * 分组名称
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: Common::GROUP_NAME_SIZE, index: 3)]
    protected $group;

    /**
     * 文件路径
     * 
     * @var string
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_ALL_REST_BYTE, index: 4)]
    protected $path;

    /**
     * 要设置的元数据
     * 
     * @var array
     */
    #[FastDFSParam(type: FastDFSParam::TYPE_FILE_META, index: 5)]
    protected $metadata;

    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 13;
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $arguments)
    {
        if (count($arguments) !== 4) {
            throw new InvalidArgumentException(sprintf(
                'expects at least 4 arguments, %d given', count($arguments)
            ));
        }

        [$flag, $group, $path, $metadata] = $arguments;

        if ($flag !== OperationFlag::OVERWRITE && $flag !== OperationFlag::MERGE) {
            throw new InvalidArgumentException('operation flag must be of `W` or `O`');
        }

        if (!is_array($metadata)) {
            throw new InvalidArgumentException('Argument #3 must be of type array');
        }

        $lineSeperator  = hex2bin(Common::LINE_SEPERATOR);
        $fieldSeperator = hex2bin(Common::FIELD_SEPERATOR);

        $metadataByte = [];
        foreach ($metadata as $key => $value) {
            $metadataByte[] = "{$key}{$fieldSeperator}{$value}";
        }

        $metadataByte = join($lineSeperator, $metadataByte);

        parent::setArguments([
            strlen($path), strlen($metadataByte), $flag, $group, $path, $metadataByte
        ]);
    }
}
