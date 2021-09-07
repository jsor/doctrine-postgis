<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

final class GeometryTypeTest extends AbstractTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'geometry';
    }

    public function testGetNormalizedPostGISColumnOptions(): void
    {
        $expected = ['geometry_type' => 'GEOMETRY', 'srid' => 0];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions());
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions([]));

        $expected = ['geometry_type' => 'POINT', 'srid' => 0];
        $this->assertEquals($expected, $this->type->getNormalizedPostGISColumnOptions(['geometry_type' => 'point']));
    }

    public function testGetNormalizedPostGISColumnOptionsCastSRIDToInteger(): void
    {
        $normalized = $this->type->getNormalizedPostGISColumnOptions(['srid' => '4326']);
        $this->assertSame(4326, $normalized['srid']);
    }
}
