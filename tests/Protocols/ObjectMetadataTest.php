<?php

namespace Tests\Ant\FastDFS\Protocols;

use Ant\FastDFS\BytesUtil;
use PHPUnit\Framework\TestCase;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\ObjectMetadata;
use Ant\FastDFS\Exceptions\ProtocolException;

class ObjectMetadataTest extends TestCase
{
    /**
     * @group disconnected
     */
    public function testGetProperty()
    {
        $object = new Foobar;
        $object->field1 = 'foobar';
        $object->field2 = 1;
        $object->field3 = '1234567890';

        $meta = new ObjectMetadata(Foobar::class);

        $this->assertEquals(Foobar::class, $meta->getClassName());
        $this->assertEquals(3, count($meta->getFields()));
        $this->assertEquals(24, $meta->getFieldTotalSize());
        $this->assertEquals(10, $meta->getDynamicTotalFieldSize($object));
        $this->assertEquals(34, $meta->getFieldsSendTotalSize($object));
    }

    /**
     * @group disconnected
     */
    public function testConflictFieldIndex()
    {
        $this->expectException(ProtocolException::class);
        $this->expectExceptionMessage('Tests\Ant\FastDFS\Protocols\ConflictFoobar field2 conflicts with the field1');

        new ObjectMetadata(ConflictFoobar::class);
    }

    /**
     * @group disconnected
     */
    public function testObjectToByte()
    {
        $object = new Foobar;
        $object->field1 = 'foobar';
        $object->field2 = 1;
        $object->field3 = '1234567890';

        $meta = new ObjectMetadata(Foobar::class);

        $this->assertEquals($this->prepareByte('foobar', 1, '1234567890'), $meta->toByte($object));
    }

    /**
     * @group disconnected
     */
    public function testByteToObject()
    {
        $meta = new ObjectMetadata(Foobar::class);

        $object = $meta->newInstance($this->prepareByte('foobar', 1, '1234567890'));

        $this->assertEquals('foobar', $object->field1);
        $this->assertEquals(1, $object->field2);
        $this->assertEquals('1234567890', $object->field3);
    }

    /**
     * @group disconnected
     */
    protected function prepareByte($field1, $field2, $field3)
    {
        $field1 = BytesUtil::padding($field1, 16);
        $field2 = BytesUtil::long2buff($field2);

        return $field1.$field2.$field3;
    }
}

class Foobar
{
    #[FastDFSParam(FastDFSParam::TYPE_STRING, 0, 16)]
    public $field1;

    #[FastDFSParam(FastDFSParam::TYPE_INT, 1)]
    public $field2;

    #[FastDFSParam(FastDFSParam::TYPE_ALL_REST_BYTE, 2)]
    public $field3;
}

class ConflictFoobar
{
    #[FastDFSParam(FastDFSParam::TYPE_STRING, 1, 16)]
    public $field1;

    #[FastDFSParam(FastDFSParam::TYPE_INT, 1)]
    public $field2;
}