<?php

namespace Ant\Tests\FastDFS\Protocols;

use Ant\FastDFS\BytesUtil;
use PHPUnit\Framework\TestCase;
use Ant\FastDFS\Commands\Command;
use Ant\FastDFS\Protocols\Request;
use Ant\FastDFS\Protocols\ObjectMetadata;
use Ant\FastDFS\Contracts\Stream as StreamContract;
use Ant\FastDFS\Contracts\Command as CommandContract;

class RequestTest extends TestCase
{
    public function testGetHeadByte()
    {
        $meta = $this->getMockBuilder(ObjectMetadata::class)
            ->setConstructorArgs([MockCommand::class])
            ->getMock();
        $meta->expects($this->once())
            ->method('getFieldsSendTotalSize')
            ->willReturn(10);

        $command = $this->getMockBuilder(CommandContract::class)->getMock();
        $command->expects($this->once())
            ->method('getCmd')
            ->willReturn(11);

        $request = new Request($meta, $command);

        $hex = ['000000000000000a', '0b', '00'];

        $this->assertSame(hex2bin(join('', $hex)), $request->getHeadByte());
    }

    public function testGetHeadByteWithInputStream()
    {
        $meta = $this->getMockBuilder(ObjectMetadata::class)
            ->setConstructorArgs([MockCommand::class])
            ->getMock();
        $meta->expects($this->once())
            ->method('getFieldsSendTotalSize')
            ->willReturn(10);

        $command = $this->getMockBuilder(CommandContract::class)->getMock();
        $command->expects($this->once())
            ->method('getCmd')
            ->willReturn(11);

        $stream = $this->getMockBuilder(StreamContract::class)->getMock();
        $stream->expects($this->once())
            ->method('getSize')
            ->willReturn(16);

        $request = new Request($meta, $command, $stream);

        $hex = ['000000000000001a', '0b', '00'];

        $this->assertSame(hex2bin(join('', $hex)), $request->getHeadByte());
    }

    public function testGetParamByte()
    {
        $meta = $this->getMockBuilder(ObjectMetadata::class)
            ->setConstructorArgs([MockCommand::class])
            ->getMock();
        $meta->expects($this->once())
            ->method('toByte')
            ->willReturn(BytesUtil::padding('foobar', 16));

        $command = $this->getMockBuilder(CommandContract::class)->getMock();

        $request = new Request($meta, $command);

        $byte = $request->getParamByte();

        $this->assertSame('foobar', trim($byte));
    }

    public function testGetInputFileStream()
    {
        $meta = $this->getMockBuilder(ObjectMetadata::class)
            ->setConstructorArgs([MockCommand::class])
            ->getMock();
        $meta->expects($this->once())
            ->method('getFieldsSendTotalSize')
            ->willReturn(10);

        $command = $this->getMockBuilder(CommandContract::class)->getMock();

        $stream = $this->getMockBuilder(StreamContract::class)->getMock();
        $stream->expects($this->once())
            ->method('getSize')
            ->willReturn(16);

        $request = new Request($meta, $command, $stream);

        $this->assertSame($stream, $request->getInputFileStream());
    }
}

class MockCommand extends Command
{
    public function getCmd(): int
    {
        return 0;
    }
}
