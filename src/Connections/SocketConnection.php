<?php

namespace Ant\FastDFS\Connections;

use RuntimeException;
use InvalidArgumentException;
use Ant\FastDFS\Contracts\Connection;
use Ant\FastDFS\Contracts\Stream as StreamContract;

/**
 * SocketConnection
 * 
 * @package Ant\FastDFS\Connections
 */
class SocketConnection implements Connection
{
    /**
     * @var Stream
     */
    protected $stream;

    /**
     * @param string $uri
     * @param int $timeout
     * @param array $context
     */
    public function __construct(string $uri, int $timeout = 30, array $context = [])
    {
        if (strpos($uri, '://') === false) {
            $uri = 'tcp://' . $uri;
        }

        $parts = parse_url($uri);
        if (!$parts || !isset($parts['scheme'], $parts['host'], $parts['port']) || $parts['scheme'] !== 'tcp') {
            throw new InvalidArgumentException("Given URI \"{$uri}\" is invalid");
        }

        $ip = trim($parts['host'], '[]');
        if (false === filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException("Given URI \"{$ip}\" does not contain a valid host IP");
        }

        $remote = "tcp://{$parts['host']}:{$parts['port']}";

        $socket = @stream_socket_client(
            $remote,
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create($context)
        );

        if (false === $socket) {
            throw new RuntimeException("Connection to {$uri} failed: {$errstr}", $errno);
        }

        $this->stream = new Stream($socket);
    }

    /**
     * @return StreamContract
     */
    public function getStream(): StreamContract
    {
        return $this->stream;
    }

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close(): void
    {
        if ($this->stream->isClosed()) {
            return;
        }

        $this->stream->close();
    }

    /**
     * 连接是否有效
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->stream->isClosed();
    }
}
