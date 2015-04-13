<?php
/**
 * Spatial Relationships and Measurements
 * http://postgis.net/docs/reference.html#Spatial_Relationships_Measurements
 */
return array(
    'ST_3DClosestPoint' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)')))",
                    'result' => array(
                        1 => 'POINT(54.6993798867619 128.935022917228 11.5475869506606)'
                    )
                )
            )
        )
    ),
    'ST_3DDistance' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT Z (1 1 1)'), ST_GeomFromEWKT('POINT Z (2 2 2)'))",
                    'result' => array(
                        1 => 1.73205080756888
                    )
                )
            )
        )
    ),
    'ST_3DDWithin' => array(
        'required_arguments' => 3,
        'total_arguments' => 3,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 1 2)'), ST_GeomFromEWKT('LINESTRING(1 5 2, 2 7 20, 1 9 100, 14 12 3)'), 10)",
                    'result' => array(
                        1 => true
                    )
                )
            )
        )
    ),
    'ST_3DDFullyWithin' => array(
        'required_arguments' => 3,
        'total_arguments' => 3,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 1 2)'), ST_GeomFromEWKT('LINESTRING(1 5 2, 2 7 20, 1 9 100, 14 12 3)'), 10)",
                    'result' => array(
                        1 => false
                    )
                )
            )
        )
    ),
    'ST_3DIntersects' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(0 0 2)'), ST_GeomFromEWKT('LINESTRING (0 0 1, 0 2 3 )'))",
                    'result' => array(
                        1 => false
                    )
                )
            )
        )
    ),
    'ST_3DLongestLine' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)')))",
                    'result' => array(
                        1 => 'LINESTRING(50 75 1000,100 100 30)'
                    )
                )
            )
        )
    ),
    'ST_3DMaxDistance' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT Z (1 1 1)'), ST_GeomFromEWKT('POINT Z (2 2 2)'))",
                    'result' => array(
                        1 => 1.73205080756888
                    )
                )
            )
        )
    ),
    'ST_3DShortestLine' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)')))",
                    'result' => array(
                        1 => 'LINESTRING(54.6993798867619 128.935022917228 11.5475869506606,100 100 30)'
                    )
                )
            )
        )
    ),
    'ST_Area' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))',2249))",
                    'result' => array(
                        1 => 928.625
                    )
                )
            )
        )
    ),
    'ST_Azimuth' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_Point(25,45), ST_Point(75,100))",
                    'result' => array(
                        1 => 0.737815060120465
                    )
                )
            )
        )
    ),
    'ST_Centroid' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('MULTIPOINT(-1 0, -1 2, -1 3, -1 4, -1 7, 0 1, 0 3, 1 1, 2 0, 6 0, 7 8, 9 8, 10 6 )')))",
                    'result' => array(
                        1 => 'POINT(2.30769230769231 3.30769230769231)'
                    )
                )
            )
        )
    ),
    'ST_ClosestPoint' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 100)'), ST_GeomFromText('LINESTRING(20 80, 98 190, 110 180, 50 75)')))",
                    'result' => array(
                        1 => 'POINT(100 100)'
                    )
                )
            )
        )
    ),
    'ST_Contains' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20), ST_GeomFromText('POINT(1 2)'))",
                    'result' => array(
                        1 => true
                    )
                )
            )
        )
    ),
    'ST_ContainsProperly' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20), ST_GeomFromText('POINT(1 2)'))",
                    'result' => array(
                        1 => true
                    )
                )
            )
        )
    ),
    'ST_Covers' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20), ST_ExteriorRing(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20)))",
                    'result' => array(
                        1 => true
                    )
                )
            )
        )
    ),
    'ST_CoveredBy' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 10), ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20))",
                    'result' => array(
                        1 => true
                    )
                )
            )
        )
    ),
    'ST_Crosses' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 2 2)'), ST_GeomFromText('LINESTRING(0 2, 2 0)'))",
                    'result' => array(
                        1 => true
                    )
                )
            )
        )
    ),
    'ST_LineCrossingDirection' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(25 169,89 114,40 70,86 43)'), ST_GeomFromText('LINESTRING(171 154,20 140,71 74,161 53)'))",
                    'result' => array(
                        1 => -3
                    )
                )
            )
        )
    ),
    'ST_Disjoint' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(0 0)'), ST_GeomFromText('LINESTRING(2 0, 0 2)'))",
                    'result' => array(
                        1 => true
                    )
                )
            )
        )
    ),
    'ST_Distance' => array(
        'required_arguments' => 2,
        'total_arguments' => 3,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(-72.1235 42.3521)', 4326), ST_GeomFromText('LINESTRING(-72.1260 42.45, -72.123 42.1546)', 4326))",
                    'result' => array(
                        1 => 0.00150567726382822
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeographyFromText('SRID=4326;POINT(-72.1235 42.3521)'), ST_GeographyFromText('SRID=4326;LINESTRING(-72.1260 42.45, -72.123 42.1546)'), false)",
                    'result' => array(
                        1 => 123.475736916397
                    )
                )
            )
        )
    ),
);
