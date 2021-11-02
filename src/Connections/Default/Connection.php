<?php

namespace Ant\FastDFS\Connections\Default;

use Throwable;
use Ant\FastDFS\Protocols\Head;
use Ant\FastDFS\Contracts\Command;
use Ant\FastDFS\Contracts\Request;
use Ant\FastDFS\Contracts\Response;
use Ant\FastDFS\Constants\Commands;
use Ant\FastDFS\Exceptions\IOException;
use Ant\FastDFS\Contracts\Stream as StreamContract;
use Ant\FastDFS\Contracts\Connection as ConnectionContract;

/**
 * SocketConnection
 *
 * @package Ant\FastDFS\Connections
 */
class Connection implements ConnectionContract
{
    /**
     * @var Stream
     */
    protected StreamContract $stream;

    /**
     * @param string $uri
     * @param int $timeout
     * @param array $context
     */
    public function __construct(string $address, int $port, int $timeout = 30, array $context = [])
    {
        $remote = "tcp://{$address}:{$port}";

        $socket = @stream_socket_client(
            $remote,
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create($context)
        );

        if (false === $socket) {
            throw new IOException("Connection to {$address}:{$port} failed: {$errstr}", $errno);
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

        $head = new Head(0, Commands::PROTO_QUIT);

        try {
            // 发送关闭连接head
            $this->stream->write($head->toBytes());
        } catch (Throwable $e) {
            // 发送退出命令失败也不做特殊处理
        } finally {
            $this->stream->close();
        }
    }

    /**
     * 连接是否有效
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->stream->isClosed()) {
            return false;
        }

        $head = new Head(0, Commands::PROTO_ACTIVE_TEST);

        try {
            $this->stream->write($head->toBytes());

            $head = Head::createFromBuffer($this->stream->read(Head::HEAD_LENGTH));

            $head->validateResponseHead();
            // 检查状态是否正常
            return $head->getStatus() === 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * 执行指定的指令,写入传入的参数,并处理响应的内容
     *
     * @param Command $command
     * @return mixed
     */
    public function executeCommand(Command $command)
    {
        $stream = $this->getStream();

        if ($stream->isClosed()) {
            throw new IOException('Has been disconnected!');
        }

        $this->writeRequest($stream, $command->getRequest());

        return $this->readResponse($stream, $command->getResponse());
    }

    /**
     * @param Stream $output
     * @param Request $request
     */
    protected function writeRequest(Stream $output, Request $request)
    {
        $output->write($request->getHeadByte());

        $param = $request->getParamByte();
        if (!empty($param)) {
            $output->write($param);
        }

        $input = $request->getInputFileStream();
        if ($input === null) {
            return;
        }

        // TODO 允许自由配置使用的内存大小
        $limit  = 256 * 1024;
        $remain = $input->getSize();

        while ($remain > 0) {
            $length = min($limit, $remain);

            $output->write($input->read($length));

            $remain -= $length;
        }
    }

    /**
     * @param Stream $input
     * @return Response
     */
    protected function readResponse(Stream $input, Response $response): Response
    {
        $head = Head::createFromBuffer($input->read(Head::HEAD_LENGTH));

        $head->validateResponseHead();

        if ($head->getLength() > 0) {
            $response->decode($input->read($head->getLength()));
        }

        return $response;
    }
}
