<?php

namespace Jsor\Doctrine\PostGIS\Types;

class GeometryTypeTest extends AbstractTypeTestCase
{
    protected function getTypeName()
    {
        return 'geometry';
    }

    public function testgetNormalizedSpatialOptions()
    {
        $expected = array('geometry_type' => 'GEOMETRY', 'srid' => 0);
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions());
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions(array()));

        $expected = array('geometry_type' => 'POINT', 'srid' => 0);
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions(array('geometry_type' => 'point')));
    }
}
