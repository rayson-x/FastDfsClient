<?php

namespace Ant\Tests\FastDFS\Protocols;

use Ant\FastDFS\Exceptions\ProtocolException;
use Ant\FastDFS\Protocols\Head;
use PHPUnit\Framework\TestCase;

class HeadTest extends TestCase
{
    public function testGetAttributes()
    {
        $head = new Head(10, 11, 0);

        $this->assertSame(10, $head->getLength());
        $this->assertSame(11, $head->getCommand());
        $this->assertSame(0, $head->getStatus());
    }

    public function testWithAttributes()
    {
        $head = new Head(10, 11, 0);

        $new = $head->withLength(100);
        $this->assertNotEquals($head, $new);
        $this->assertSame(100, $new->getLength());

        $new = $head->withCommand(12);
        $this->assertNotEquals($head, $new);
        $this->assertSame(12, $new->getCommand());

        $new = $head->withStatus(1);
        $this->assertNotEquals($head, $new);
        $this->assertSame(1, $new->getStatus());
    }

    public function testHeadToBinary()
    {
        $hex  = ['00', '00', '00', '00', '00', '00', '04', 'd2', '10', '00'];
        $head = new Head(1234, 16, 0);

        $this->assertEquals(hex2bin(join('', $hex)), $head->toBytes());
    }

    public function testBinaryToHead()
    {
        $hex    = ['00', '00', '00', '00', '00', '00', '04', 'd2', '10', '00'];
        $binary = hex2bin(join('', $hex));
        $head   = Head::createFromBuffer($binary);

        $this->assertSame(1234, $head->getLength());
        $this->assertSame(16, $head->getCommand());
        $this->assertSame(0, $head->getStatus());
    }

    public function testErrorBinaryToHaed()
    {
        $this->expectException(ProtocolException::class);

        $hex    = ['00', '00', '00', '00', '00', '00', '04', 'd2', '10'];
        $binary = hex2bin(join('', $hex));
        Head::createFromBuffer($binary);
    }
}
