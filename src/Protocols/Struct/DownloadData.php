<?php

namespace Ant\FastDFS\Protocols\Struct;

use Ant\FastDFS\Protocols\Response;

/**
 * @package Ant\FastDFS\Protocols\Struct
 */
class DownloadData extends Response
{
    /**
     * 下载文件数据
     * 
     * @var string
     */
    public $data;

    /**
     * {@inheritdoc}
     */
    public function decode(string $buffer)
    {
        $this->data = $buffer;
    }
}
