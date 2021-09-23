<?php

namespace Ant\FastDFS\Commands\Storage;

/**
 * 创建一个支持断点续传的文件
 *
 * @package Ant\FastDFS\Commands\Storage
 */
class UploadAppendable extends Upload
{
    /**
     * {@inheritdoc}
     */
    public function getCmd(): int
    {
        return 23;
    }
}
