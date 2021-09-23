<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class FileMetadataSet extends Response
{
    #[FastDFSParam(type: FastDFSParam::TYPE_FILE_META)]
    public $metadata;
}
