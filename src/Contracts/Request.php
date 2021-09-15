<?php

namespace Ant\FastDFS\Contracts;

/**
 * Request interface
 * 
 * 因为存在只有协议头的情况(close指令)
 * 所以把协议头,传输参数与文件内容分为三个方法
 * 每一段进行是否为空判断,不为空才会进行发送
 * 
 * @package Ant\FastDFS\Contracts
 */
interface Request
{
    /**
     * 获取协议头数据
     * 
     * @return string
     */
    public function getHeadByte(): string;

    /**
     * 获取参数数据
     * 
     * @return string
     */
    public function getParamByte(): string;

    /**
     * 获取输入文件流
     * 
     * @return Stream|null
     */
    public function getInputFileStream(): ?Stream;
}
