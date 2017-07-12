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
        $expected = array('geometry_type' => 'GEOMETRY', 'srid' => 0);
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions());
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(array()));

        $expected = array('geometry_type' => 'POINT', 'srid' => 0);
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(array('geometry_type' => 'point')));
    }

    public function testGetNormalizedPostGISColumnOptionsCastSRIDToInteger()
    {
        $normalized = $this->type->getNormalizedPostGISColumnOptions(array('srid' => '4326'));
        $this->assertSame(4326, $normalized['srid']);
    }
}
