<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\AbstractTestCase;

class RasterTypeTest extends AbstractTestCase
{
    protected ?Type $type = null;

    protected function setUp(): void
    {
        $this->_registerTypes();

        $this->type = Type::getType('raster');
    }

    public function testGetSQLDeclaration(): void
    {
        $this->assertEquals('raster', $this->type->getSqlDeclaration([], $this->getPlatformMock()));
    }

    public function testConvertToPHPValue(): void
    {
        $this->assertIsString($this->type->convertToPHPValue('foo', $this->getPlatformMock()));
        $this->assertIsString($this->type->convertToPHPValue('', $this->getPlatformMock()));
    }

    public function testConvertToDatabaseValue(): void
    {
        $this->assertIsString($this->type->convertToDatabaseValue('foo', $this->getPlatformMock()));
        $this->assertIsString($this->type->convertToDatabaseValue('', $this->getPlatformMock()));
    }

    public function testNullConversion(): void
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->getPlatformMock()));
    }

    public function testConvertToPHPValueSQL(): void
    {
        $this->assertTrue($this->type->canRequireSQLConversion());

        $this->assertEquals('foo', $this->type->convertToPHPValueSQL('foo', $this->getPlatformMock()));
    }

    public function testConvertToDatabaseValueSQL(): void
    {
        $this->assertTrue($this->type->canRequireSQLConversion());

        $this->assertEquals('foo::raster', $this->type->convertToDatabaseValueSQL('foo', $this->getPlatformMock()));
    }
}
