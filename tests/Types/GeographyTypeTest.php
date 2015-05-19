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

    public function testgetNormalizedSpatialOptions()
    {
        $expected = array('geometry_type' => 'GEOMETRY', 'srid' => 4326);
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions());
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions(array()));
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions(array('srid' => 0)));

        $expected = array('geometry_type' => 'POINT', 'srid' => 4326);
        $this->assertEquals($expected, $this->type->getNormalizedSpatialOptions(array('geometry_type' => 'point')));
    }
}
