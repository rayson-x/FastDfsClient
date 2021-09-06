<?php

namespace Ant\FastDFS\Contracts;

/**
 * @package Ant\FastDFS\Contracts
 */
interface Command
{
    /**
     * 执行指令
     * 
     * @param Connection $conn
     * @return void
     */
    public function execute(Connection $conn): void;
}
