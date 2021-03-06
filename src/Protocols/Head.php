<?php

namespace Ant\FastDFS\Protocols;

use Ant\FastDFS\BytesUtil;
use Ant\FastDFS\Constants\Commands;
use Ant\FastDFS\Constants\ErrorCode;
use Ant\FastDFS\Exceptions\IOException;
use Ant\FastDFS\Exceptions\ProtocolException;
use Ant\FastDFS\Exceptions\ServerException;

/**
 * FastDFS Head Protocol
 *
 * @package Ant\FastDFS\Protocols
 */
class Head
{
    // 协议头总长
    public const HEAD_LENGTH = 10;
    // 传输报文长度
    private const PKG_LEN_SIZE = 8;
    // 指令占位索引
    private const COMMAND_INDEX = 9;
    // 状态占位索引
    private const STATUS_INDEX = 10;

    /**
     * head长度
     *
     * @var int
     */
    protected int $length;

    /**
     * 指令
     *
     * @var int
     */
    protected int $command;

    /**
     * 状态
     *
     * @var int
     */
    protected int $status;

    /**
     * @param string $buffer
     * @return Head
     */
    public static function createFromBuffer(string $buffer): Head
    {
        if (strlen($buffer) !== static::HEAD_LENGTH) {
            throw new ProtocolException('recv package size != ' . static::HEAD_LENGTH);
        }

        $bytes   = unpack('C10', $buffer);
        $length  = BytesUtil::buff2long(mb_substr($buffer, 0, static::PKG_LEN_SIZE));
        $command = $bytes[static::COMMAND_INDEX];
        $status  = $bytes[static::STATUS_INDEX];

        return new Head($length, $command, $status);
    }

    /**
     * @param int $length
     * @param int $command
     * @param int $status
     */
    public function __construct(int $length, int $command, int $status = 0)
    {
        $this->length  = $length;
        $this->command = $command;
        $this->status  = $status;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getCommand(): int
    {
        return $this->command;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $length
     * @return Head
     */
    public function withLength(int $length): Head
    {
        if ($length === $this->length) {
            return $this;
        }

        $new = clone $this;

        $new->length = $length;

        return $new;
    }

    /**
     * @param int $command
     * @return Head
     */
    public function withCommand(int $command): Head
    {
        if ($command === $this->command) {
            return $this;
        }

        $new = clone $this;

        $new->command = $command;

        return $new;
    }

    /**
     * @param int $status
     * @return Head
     */
    public function withStatus(int $status): Head
    {
        if ($status === $this->status) {
            return $this;
        }

        $new = clone $this;

        $new->status = $status;

        return $new;
    }

    /**
     * @return string
     */
    public function toBytes(): string
    {
        $length = BytesUtil::long2buff($this->length);

        return $length . pack('CC', $this->command, $this->status);
    }

    /**
     * 校验服务器响应头
     *
     * @return void
     * @throws FastDFSException
     */
    public function validateResponseHead()
    {
        if ($this->command !== Commands::PROTO_RESPONSE) {
            throw new IOException("recv cmd: {$this->command} is not correct");
        }

        if ($this->status !== ErrorCode::SUCCESS) {
            throw ServerException::byCode($this->status);
        }

        if ($this->length < 0) {
            throw new IOException("recv body length: {$this->length} < 0!");
        }
    }
}
