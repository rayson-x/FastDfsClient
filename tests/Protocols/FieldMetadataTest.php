<?php

namespace Tests\Ant\FastDFS\Protocols;

use ReflectionProperty;
use PHPUnit\Framework\TestCase;
use Ant\FastDFS\Constants\Common;
use Ant\FastDFS\Protocols\FastDFSParam;
use Ant\FastDFS\Protocols\FieldMetadata;
use Ant\FastDFS\Exceptions\ProtocolException;

class FieldMetadataTest extends TestCase
{
    public function testGetProperty()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_INT, 1, 4);
        $meta     = new FieldMetadata($property, $param, 3);

        $this->assertEquals('bar', $meta->getName());
        $this->assertEquals(1, $meta->getIndex());
        $this->assertEquals(4, $meta->getMax());
        $this->assertEquals(3, $meta->getOffset());
        $this->assertEquals($property, $meta->getProperty());
    }

    public function testInvalidField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(10, 1, 4);
        $meta     = new FieldMetadata($property, $param, 3);

        $this->expectException(ProtocolException::class);
        $this->expectExceptionMessage('Type errors cannot be converted to bytes');

        $meta->toByte(new Foo(null));
    }

    public function testStringField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_STRING, 0, 16);
        $meta     = new FieldMetadata($property, $param, 0);

        // 检查类型特性是否正确
        $this->assertEquals(16, $meta->getSize());
        $this->assertEquals(FastDFSParam::TYPE_STRING, $meta->getType());
        $this->assertNotTrue($meta->isDynamicField());

        // 对象转换为byte
        $object = new Foo('foobar');
        $this->assertEquals(16, strlen($meta->toByte($object)));
        $this->assertEquals('foobar' . hex2bin('00000000000000000000'), $meta->toByte($object));

        // byte变回对象
        $byte = 'foobar' . hex2bin('00000000000000000000');
        $this->assertEquals('foobar', $meta->getValue($byte));
    }

    public function testIntField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_INT, 0);
        $meta     = new FieldMetadata($property, $param, 0);

        // 检查类型特性是否正确
        $this->assertEquals(Common::LONG_SIZE, $meta->getSize());
        $this->assertEquals(FastDFSParam::TYPE_INT, $meta->getType());
        $this->assertNotTrue($meta->isDynamicField());

        // 对象转换为byte
        $object = new Foo(10);
        $this->assertEquals(8, strlen($meta->toByte($object)));
        $this->assertEquals(hex2bin('000000000000000a'), $meta->toByte($object));

        // byte变回对象
        $byte = hex2bin('0000000000000010');
        $this->assertEquals(16, $meta->getValue($byte));
    }

    public function testByteField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_BYTE, 0);
        $meta     = new FieldMetadata($property, $param, 0);

        // 检查类型特性是否正确
        $this->assertEquals(Common::BYTE_SIZE, $meta->getSize());
        $this->assertEquals(FastDFSParam::TYPE_BYTE, $meta->getType());
        $this->assertNotTrue($meta->isDynamicField());

        // 对象转换为byte
        $object = new Foo(hex2bin('01'));
        $this->assertEquals(1, strlen($meta->toByte($object)));
        $this->assertEquals(hex2bin('01'), $meta->toByte($object));

        // byte变回对象
        $byte = hex2bin('31');
        $this->assertEquals('1', $meta->getValue($byte));
    }

    public function testBoolField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_BOOL, 0);
        $meta     = new FieldMetadata($property, $param, 0);

        // 检查类型特性是否正确
        $this->assertEquals(Common::BYTE_SIZE, $meta->getSize());
        $this->assertEquals(FastDFSParam::TYPE_BOOL, $meta->getType());
        $this->assertNotTrue($meta->isDynamicField());

        // 对象转换为byte
        $object = new Foo(true);
        $this->assertEquals(1, strlen($meta->toByte($object)));
        $this->assertEquals(hex2bin('01'), $meta->toByte($object));

        // byte变回对象
        $this->assertTrue($meta->getValue(hex2bin('01')));
        $this->assertNotTrue($meta->getValue(hex2bin('00')));
    }

    public function testNullableField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_NULLABLE, 0, 16);
        $meta     = new FieldMetadata($property, $param, 0);

        // 检查类型特性是否正确
        $this->assertEquals(0, $meta->getSize());
        $this->assertEquals(FastDFSParam::TYPE_NULLABLE, $meta->getType());
        $this->assertTrue($meta->isDynamicField());

        // 空值转换为byte
        $object = new Foo('');
        $this->assertEquals(0, $meta->getDynamicSize($object));
        $this->assertEquals(0, strlen($meta->toByte($object)));
        $this->assertEquals('', $meta->toByte($object));

        // 不为空转换为byte
        $object = new Foo('foobar');
        $this->assertEquals(16, $meta->getDynamicSize($object));
        $this->assertEquals(16, strlen($meta->toByte($object)));
        $this->assertEquals('foobar' . hex2bin('00000000000000000000'), $meta->toByte($object));

        // byte变回对象
        $this->expectException(ProtocolException::class);
        $this->expectExceptionMessage('Nullable does not support conversion');

        $meta->getValue(hex2bin('01'));
    }

    public function testMetadataField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_FILE_META, 0);
        $meta     = new FieldMetadata($property, $param, 0);

        // 检查类型特性是否正确
        $this->assertEquals(0, $meta->getSize());
        $this->assertEquals(FastDFSParam::TYPE_FILE_META, $meta->getType());
        $this->assertTrue($meta->isDynamicField());

        // 格式化
        $lineSeperator  = hex2bin(Common::LINE_SEPERATOR);
        $fieldSeperator = hex2bin(Common::FIELD_SEPERATOR);

        $byte     = [];
        $metadata = ['foo' => 'bar', 'bar' => 'foo'];
        foreach ($metadata as $key => $value) {
            $byte[] = "{$key}{$fieldSeperator}{$value}";
        }
        $byte = join($lineSeperator, $byte);

        // 对象转换为byte
        $object = new Foo($byte);
        $this->assertEquals(15, strlen($meta->toByte($object)));
        $this->assertEquals($byte, $meta->toByte($object));

        // byte变回对象
        $this->assertEquals($metadata, $meta->getValue($byte));
    }

    public function testAllRestByteField()
    {
        $property = new ReflectionProperty(Foo::class, 'bar');
        $param    = new FastDFSParam(FastDFSParam::TYPE_ALL_REST_BYTE, 0);
        $meta     = new FieldMetadata($property, $param, 0);

        // 检查类型特性是否正确
        $this->assertEquals(0, $meta->getSize());
        $this->assertEquals(FastDFSParam::TYPE_ALL_REST_BYTE, $meta->getType());
        $this->assertTrue($meta->isDynamicField());

        // 不为空转换为byte
        $object = new Foo('foobar');
        $this->assertEquals(6, $meta->getDynamicSize($object));
        $this->assertEquals(6, strlen($meta->toByte($object)));
        $this->assertEquals('foobar', $meta->toByte($object));

        // byte变回对象
        $this->assertEquals('foobar', $meta->getValue('foobar'));
    }
}

class Foo
{
    public function __construct(public $bar)
    {

    }
}
