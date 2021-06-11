<?php

namespace Jsor\Doctrine\PostGIS\Test\Types;

use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Test\AbstractTestCase;

class RasterTypeTest extends AbstractTestCase
{
    protected $type;

    protected function setUp():void
    {
        $this->_registerTypes();

        $this->type = Type::getType('raster');
    }

    public function testGetSQLDeclaration()
    {
        $this->assertEquals('raster', $this->type->getSqlDeclaration([], $this->getPlatformMock()));
    }

    public function testConvertToPHPValue()
    {
        $this->assertIsString($this->type->convertToPHPValue('foo', $this->getPlatformMock()));
        $this->assertIsString($this->type->convertToPHPValue('', $this->getPlatformMock()));
    }

    public function testConvertToDatabaseValue()
    {
        $this->assertIsString($this->type->convertToDatabaseValue('foo', $this->getPlatformMock()));
        $this->assertIsString($this->type->convertToDatabaseValue('', $this->getPlatformMock()));
    }

    public function testNullConversion()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->getPlatformMock()));
    }

    public function testConvertToPHPValueSQL()
    {
        $this->assertTrue($this->type->canRequireSQLConversion());

        $this->assertEquals('foo', $this->type->convertToPHPValueSQL('foo', $this->getPlatformMock()));
    }

    public function testConvertToDatabaseValueSQL()
    {
        $this->assertTrue($this->type->canRequireSQLConversion());

        $this->assertEquals('foo::raster', $this->type->convertToDatabaseValueSQL('foo', $this->getPlatformMock()));
    }
}
