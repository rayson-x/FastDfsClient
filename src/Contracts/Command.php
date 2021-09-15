<?php

namespace Ant\FastDFS\Contracts;

/**
 * @package Ant\FastDFS\Contracts
 */
interface Command
{
    /**
     * 返回FastDFS具体指令
     *
     * @return string
     */
    public function getCmd(): int;

    /**
     * 设置指令使用的参数.
     *
     * @param array.
     */
    public function setArguments(array $arguments);

    /**
     * 返回Request对象,Request负责生成二进制数据
     * 保证不同实现的Connection不需要关注协议层的实现
     *
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * 返回可以解析Response对象
     *
     * @return Response
     */
    public function getResponse(): Response;
}
