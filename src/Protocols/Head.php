<?php

namespace Ant\FastDFS\Protocols;

use Ant\FastDFS\Exceptions\ProtocolException;

/**
 * @package Ant\FastDFS\Protocols
 * 
 * FastDFS Head Protocol
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
    protected $length;

    /**
     * 指令
     *
     * @var int
     */
    protected $command;

    /**
     * 状态
     *
     * @var int
     */
    protected $status;

    /**
     * @param string $buffer
     * @return Head
     */
    public static function createFromBuffer(string $buffer)
    {
        if (strlen($buffer) !== static::HEAD_LENGTH) {
            throw new ProtocolException('recv package size != ' . static::HEAD_LENGTH);
        }

        $bytes   = unpack('C10', $buffer);
        $length  = self::unpackU64(mb_substr($buffer, 0, static::PKG_LEN_SIZE));
        $command = $bytes[static::COMMAND_INDEX];
        $status  = $bytes[static::STATUS_INDEX];

        return new Head($length, $command, $status);
    }

    /**
     * @param string $value
     * @return int
     */
    private static function unpackU64(string $value): int
    {
        list($hi, $lo) = array_values(unpack("N*N*", $value));

        // 4294967295是32位最大自然数
        return (int) bcadd($lo, bcmul($hi, "4294967296"));
    }

    /**
     * @param int $length
     * @param int $command
     * @param int $status
     */
    public function __construct(int $length, int $command, int $status)
    {
        $this->length  = $length;
        $this->command = $command;
        $this->status  = $status;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $length
     * @return Head
     */
    public function withLength(int $length)
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
    public function withCommand(int $command)
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
    public function withStatus(int $status)
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
        $length = pack("NN", $this->length >> 32, $this->length & 0xFFFFFFFF);

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
        // TODO
    }
}
