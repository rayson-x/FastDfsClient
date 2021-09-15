<?php

namespace Ant\FastDFS\Contracts;

/**
 * Response interface
 * 
 * @package Ant\FastDFS\Contracts
 */
interface Response
{
    /**
     * @param string $buffer
     */
    public function decode(string $buffer);
}
