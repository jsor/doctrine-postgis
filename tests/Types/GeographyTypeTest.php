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
        $expected = array('geometry_type' => 'GEOMETRY', 'srid' => 4326);
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions());
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(array()));
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(array('srid' => 0)));

        $expected = array('geometry_type' => 'POINT', 'srid' => 4326);
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(array('geometry_type' => 'point')));
    }

    public function testGetNormalizedPostGISColumnOptionsCastSRIDToInteger()
    {
        $normalized = $this->type->getNormalizedPostGISColumnOptions(array('srid' => '4326'));
        $this->assertSame(4326, $normalized['srid']);
    }
}
