<?php

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\AbstractTestCase;

class RasterTypeTest extends AbstractTestCase
{
    protected $type;

    protected function setUp()
    {
        $this->_registerTypes();

        $this->type = Type::getType('raster');
    }

    public function testGetSQLDeclaration()
    {
        $this->assertEquals('raster', $this->type->getSqlDeclaration(array(), $this->getPlatformMock()));
    }

    public function testConvertToPHPValue()
    {
        $this->assertInternalType('string', $this->type->convertToPHPValue('foo', $this->getPlatformMock()));
        $this->assertInternalType('string', $this->type->convertToPHPValue('', $this->getPlatformMock()));
    }

    public function testConvertToDatabaseValue()
    {
        $this->assertInternalType('string', $this->type->convertToDatabaseValue('foo', $this->getPlatformMock()));
        $this->assertInternalType('string', $this->type->convertToDatabaseValue('', $this->getPlatformMock()));
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
