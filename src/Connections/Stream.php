<?php

namespace Ant\FastDFS\Connections;

use RuntimeException;
use InvalidArgumentException;
use Ant\FastDFS\Exceptions\IOException;
use Ant\FastDFS\Contracts\Stream as StreamContract;

/**
 * Stream
 *
 * @package Ant\FastDFS\Connections
 */
class Stream implements StreamContract
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * @var int|null
     */
    protected ?int $size;

    /**
     * @var array
     */
    protected array $customMetadata = [];

    /**
     * @var bool
     */
    protected bool $closed = false;

    /**
     * @param resource $stream
     * @param array    $options
     */
    public function __construct($stream, array $options = [])
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Stream must be a resource');
        }

        if (isset($options['size'])) {
            $this->size = $options['size'];
        }

        $this->stream         = $stream;
        $this->customMetadata = $options['metadata'] ?? [];
    }

    /**
     * 读取指定长度的内容
     *
     * @param int $length
     * @return string
     */
    public function read(int $length): string
    {
        $this->checkStream();

        if ($length < 0) {
            throw new RuntimeException('Length parameter cannot be negative');
        }

        if (0 === $length) {
            return '';
        }

        $data = fread($this->stream, $length);

        if (false === $data) {
            throw new IOException('Unable to read from stream');
        }

        return $data;
    }

    /**
     * 写入数据
     *
     * @param string $buffer
     * @return int
     */
    public function write(string $buffer): int
    {
        $this->checkStream();

        if (empty($buffer)) {
            return 0;
        }

        // 写入后需要重新计算流的总长度
        $this->size = null;

        $length = fwrite($this->stream, $buffer);

        if ($length === false) {
            throw new IOException('Unable to write from stream');
        }

        return $length;
    }

    /**
     * 是否结束
     *
     * @return bool
     */
    public function eof(): bool
    {
        $this->checkStream();

        return feof($this->stream);
    }

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close(): void
    {
        if (empty($this->stream)) {
            return;
        }

        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        $this->size   = null;
        $this->stream = null;
        $this->closed = true;
    }

    /**
     * 是否关闭连接
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * 获取流的长度
     *
     * @return int|null
     */
    public function getSize(): ?int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (empty($this->stream)) {
            return null;
        }

        $stats = fstat($this->stream);

        if (empty($stats['size'])) {
            return null;
        }

        return $this->size = $stats['size'];
    }

    /**
     * 获取流的元数据
     *
     * @return array|string|null
     */
    public function getMetadata($key = null): array | string | null
    {
        if (empty($this->stream)) {
            return $key !== null ? null : [];
        }

        $meta = array_merge(
            $this->customMetadata,
            stream_get_meta_data($this->stream)
        );

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    /**
     * @return void
     * @throws IOException
     */
    protected function checkStream(): void
    {
        if ($this->closed || empty($this->stream)) {
            throw new IOException('Stream is detached');
        }
    }
}
