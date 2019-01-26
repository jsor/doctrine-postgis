<?php

namespace Jsor\Doctrine\PostGIS\Types;

class GeometryTypeTest extends AbstractTypeTestCase
{
    protected function getTypeName()
    {
        return 'geometry';
    }

    public function testGetNormalizedPostGISColumnOptions()
    {
        $expected = ['geometry_type' => 'GEOMETRY', 'srid' => 0];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions());
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions([]));

        $expected = ['geometry_type' => 'POINT', 'srid' => 0];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(['geometry_type' => 'point']));
    }

    public function testGetNormalizedPostGISColumnOptionsCastSRIDToInteger()
    {
        $normalized = $this->type->getNormalizedPostGISColumnOptions(['srid' => '4326']);
        $this->assertSame(4326, $normalized['srid']);
    }
}
