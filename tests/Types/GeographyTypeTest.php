<?php

namespace Jsor\Doctrine\PostGIS\Types;

class GeographyTypeTest extends AbstractTypeTestCase
{
    protected function getTypeName()
    {
        return 'geography';
    }

    public function testConvertToDatabaseValueSQL()
    {
        $this->assertTrue($this->type->canRequireSQLConversion());

        $this->assertEquals('ST_GeographyFromText(foo)', $this->type->convertToDatabaseValueSQL('foo', $this->getPlatformMock()));
    }

    public function testGetNormalizedPostGISColumnOptions()
    {
        $expected = ['geometry_type' => 'GEOMETRY', 'srid' => 4326];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions());
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions([]));
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(['srid' => 0]));

        $expected = ['geometry_type' => 'POINT', 'srid' => 4326];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(['geometry_type' => 'point']));
    }

    public function testGetNormalizedPostGISColumnOptionsCastSRIDToInteger()
    {
        $normalized = $this->type->getNormalizedPostGISColumnOptions(['srid' => '4326']);
        $this->assertSame(4326, $normalized['srid']);
    }
}
