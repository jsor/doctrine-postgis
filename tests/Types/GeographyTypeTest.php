<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

final class GeographyTypeTest extends AbstractTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'geography';
    }

    public function testConvertToDatabaseValueSQL(): void
    {
        $this->assertTrue($this->type->canRequireSQLConversion());

        $this->assertEquals('ST_GeographyFromText(foo)', $this->type->convertToDatabaseValueSQL('foo', $this->getPlatformMock()));
    }

    public function testGetNormalizedPostGISColumnOptions(): void
    {
        $expected = ['geometry_type' => 'GEOMETRY', 'srid' => 4326];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions());
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions([]));
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(['srid' => 0]));

        $expected = ['geometry_type' => 'POINT', 'srid' => 4326];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(['geometry_type' => 'point']));
    }

    public function testGetNormalizedPostGISColumnOptionsCastSRIDToInteger(): void
    {
        $normalized = $this->type->getNormalizedPostGISColumnOptions(['srid' => '4326']);
        $this->assertSame(4326, $normalized['srid']);
    }
}
