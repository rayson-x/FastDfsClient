<?php

namespace Ant\Tests\FastDFS\Protocols;

use Ant\FastDFS\BytesUtil;
use PHPUnit\Framework\TestCase;
use Ant\FastDFS\Protocols\Response;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\MetadataMapper;

class ResponseTest extends TestCase
{
    public function testReadResponse()
    {
        $name  = BytesUtil::padding('foobar', 16);
        $id    = BytesUtil::long2buff(1);
        $index = 0x01;

        $response = new MockResponse(new MetadataMapper());

        $response->decode($name . $id . $index);

        $this->assertEquals('foobar', $response->name);
        $this->assertEquals(1, $response->id);
        $this->assertEquals(0x01, $response->index);
    }
}

class MockResponse extends Response
{
    #[FastDFSParam(type: FastDFSParam::TYPE_STRING, max: 16, index: 0)]
    public $name;

    #[FastDFSParam(type: FastDFSParam::TYPE_INT, index: 1)]
    public $id;

    #[FastDFSParam(type: FastDFSParam::TYPE_BYTE, index: 2)]
    public $index;
}
