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
        $expected = array('spatial_type' => 'GEOMETRY', 'spatial_srid' => 0);
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions());
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions(array()));

        $expected = array('spatial_type' => 'POINT', 'spatial_srid' => 0);
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions(array('spatial_type' => 'point')));
    }
}
