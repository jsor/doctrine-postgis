<?php

declare(strict_types=1);

/**
 * Spatial Relationships and Measurements
 * http://postgis.net/docs/reference.html#Spatial_Relationships_Measurements.
 */
return [
    'ST_3DClosestPoint' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)'))) AS value",
                    'result' => [
                        'value' => 'POINT(54.6993798867619 128.935022917228 11.5475869506606)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)'))) AS value",
                    'result' => [
                        'value' => 'POINT(54.69937988676193 128.93502291722837 11.547586950660556)',
                    ],
                ],
            ],
        ],
    ],
    'ST_3DDistance' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT Z (1 1 1)'), ST_GeomFromEWKT('POINT Z (2 2 2)')) AS value",
                    'result' => [
                        'value' => 1.73205080756888,
                    ],
                ],
            ],
        ],
    ],
    'ST_3DDWithin' => [
        'required_arguments' => 3,
        'total_arguments' => 3,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 1 2)'), ST_GeomFromEWKT('LINESTRING(1 5 2, 2 7 20, 1 9 100, 14 12 3)'), 10) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_3DDFullyWithin' => [
        'required_arguments' => 3,
        'total_arguments' => 3,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 1 2)'), ST_GeomFromEWKT('LINESTRING(1 5 2, 2 7 20, 1 9 100, 14 12 3)'), 10) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
            ],
        ],
    ],
    'ST_3DIntersects' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(0 0 2)'), ST_GeomFromEWKT('LINESTRING (0 0 1, 0 2 3 )')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
            ],
        ],
    ],
    'ST_3DLongestLine' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(50 75 1000,100 100 30)',
                    ],
                ],
            ],
        ],
    ],
    'ST_3DMaxDistance' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT Z (1 1 1)'), ST_GeomFromEWKT('POINT Z (2 2 2)')) AS value",
                    'result' => [
                        'value' => 1.73205080756888,
                    ],
                ],
            ],
        ],
    ],
    'ST_3DShortestLine' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(54.6993798867619 128.935022917228 11.5475869506606,100 100 30)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING (20 80 20, 98 190 1, 110 180 3, 50 75 1000)'), ST_GeomFromEWKT('POINT(100 100 30)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(54.69937988676193 128.93502291722837 11.547586950660556,100 100 30)',
                    ],
                ],
            ],
        ],
    ],
    'ST_Area' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))',2249)) AS value",
                    'result' => [
                        'value' => 928.625,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeographyFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))'), true) AS value",
                    'result' => [
                        'value' => 7635253966144.121,
                    ],
                ],
            ],
        ],
    ],
    'ST_Azimuth' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => 'SELECT {function}(ST_Point(25,45), ST_Point(75,100)) AS value',
                    'result' => [
                        'value' => 0.737815060120465,
                    ],
                ],
            ],
        ],
    ],
    'ST_Centroid' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('MULTIPOINT(-1 0, -1 2, -1 3, -1 4, -1 7, 0 1, 0 3, 1 1, 2 0, 6 0, 7 8, 9 8, 10 6 )'))) AS value",
                    'result' => [
                        'value' => 'POINT(2.30769230769231 3.30769230769231)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('MULTIPOINT(-1 0, -1 2, -1 3, -1 4, -1 7, 0 1, 0 3, 1 1, 2 0, 6 0, 7 8, 9 8, 10 6 )'))) AS value",
                    'result' => [
                        'value' => 'POINT(2.307692307692308 3.307692307692308)',
                    ],
                ],
            ],
        ],
    ],
    'ST_ClosestPoint' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 100)'), ST_GeomFromText('LINESTRING(20 80, 98 190, 110 180, 50 75)'))) AS value",
                    'result' => [
                        'value' => 'POINT(100 100)',
                    ],
                ],
            ],
        ],
    ],
    'ST_Contains' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20), ST_GeomFromText('POINT(1 2)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_ContainsProperly' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20), ST_GeomFromText('POINT(1 2)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Covers' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20), ST_ExteriorRing(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20))) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_CoveredBy' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 2)'), 10), ST_Buffer(ST_GeomFromText('POINT(1 2)'), 20)) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Crosses' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 2 2)'), ST_GeomFromText('LINESTRING(0 2, 2 0)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_LineCrossingDirection' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0', 'postgis-3.1', 'postgis-3.2'],
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(25 169,89 114,40 70,86 43)'), ST_GeomFromText('LINESTRING(171 154,20 140,71 74,161 53)')) AS value",
                    'result' => [
                        'value' => -3,
                    ],
                ],
                [
                    'groups' => ['postgis-3.3'],
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(25 169,89 114,40 70,86 43)'), ST_GeomFromText('LINESTRING(171 154,20 140,71 74,161 53)')) AS value",
                    'result' => [
                        'value' => 3.0,
                    ],
                ],
            ],
        ],
    ],
    'ST_Disjoint' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(0 0)'), ST_GeomFromText('LINESTRING(2 0, 0 2)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Distance' => [
        'required_arguments' => 2,
        'total_arguments' => 3,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(-72.1235 42.3521)', 4326), ST_GeomFromText('LINESTRING(-72.1260 42.45, -72.123 42.1546)', 4326)) AS value",
                    'result' => [
                        'value' => 0.00150567726382822,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeographyFromText('SRID=4326;POINT(-72.1235 42.3521)'), ST_GeographyFromText('SRID=4326;LINESTRING(-72.1260 42.45, -72.123 42.1546)'), false) AS value",
                    'result' => [
                        'value' => 123.475736916,
                    ],
                ],
            ],
        ],
    ],
    'ST_HausdorffDistance' => [
        'required_arguments' => 2,
        'total_arguments' => 3,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING (130 0, 0 0, 0 150)'), ST_GeomFromText('LINESTRING (10 10, 10 150, 130 10)'), 0.5) AS value",
                    'result' => [
                        'value' => 70,
                    ],
                ],
            ],
        ],
    ],
    'ST_MaxDistance' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(0 0)'), ST_GeomFromText('LINESTRING(2 0, 0 2)')) AS value",
                    'result' => [
                        'value' => 2,
                    ],
                ],
            ],
        ],
    ],
    'ST_DistanceSphere' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(-72.1235 42.3521)', 4326), ST_GeomFromText('LINESTRING(-72.1260 42.45, -72.123 42.1546)', 4326)) AS value",
                    'result' => [
                        'value' => 123.475736916,
                    ],
                ],
            ],
        ],
    ],
    'ST_DistanceSpheroid' => [
        'required_arguments' => 2,
        'total_arguments' => 3,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(-72.1235 42.3521)', 4326), ST_GeomFromText('LINESTRING(-72.1260 42.45, -72.123 42.1546)', 4326), 'SPHEROID[\"WGS 84\",6378137,298.257223563]') AS value",
                    'result' => [
                        'value' => 123.802076746845,
                    ],
                ],
            ],
        ],
    ],
    'ST_DFullyWithin' => [
        'required_arguments' => 3,
        'total_arguments' => 3,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(1 1)'), ST_GeomFromText('LINESTRING(1 5, 2 7, 1 9, 14 12)'), 20) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_DWithin' => [
        'required_arguments' => 3,
        'total_arguments' => 4,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(1 1)'), ST_GeomFromText('LINESTRING(1 5, 2 7, 1 9, 14 12)'), 10) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Equals' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 10 10)'), ST_GeomFromText('LINESTRING(0 0, 5 5, 10 10)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_HasArc' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_Collect('LINESTRING(1 2, 3 4, 5 6)', 'CIRCULARSTRING(1 1, 2 3, 4 5, 6 7, 5 6)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Intersects' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(0 0)'), ST_GeomFromText('LINESTRING(0 0, 0 2)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(0 0)'), ST_GeomFromText('LINESTRING(2 0, 0 2)')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
            ],
        ],
    ],
    'ST_Length' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416)', 2249)) AS value",
                    'result' => [
                        'value' => 122.630744000095,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeographyFromText('SRID=4326;LINESTRING(-72.1260 42.45, -72.1240 42.45666, -72.123 42.1546)'), false) AS value",
                    'result' => [
                        'value' => 34346.2060960742,
                    ],
                ],
            ],
        ],
    ],
    'ST_3DLength' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(743238 2967416 1,743238 2967450 1,743265 2967450 3,743265.625 2967416 3,743238 2967416 3)',2249)) AS value",
                    'result' => [
                        'value' => 122.704716741457,
                    ],
                ],
            ],
        ],
    ],
    'ST_LengthSpheroid' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('MULTILINESTRING((-118.584 38.374,-118.583 38.5),(-71.05957 42.3589 , -71.061 43))'),'SPHEROID[\"GRS_1980\",6378137,298.257222101]') AS value",
                    'result' => [
                        'value' => 85204.5207711805,
                    ],
                ],
            ],
        ],
    ],
    /*'ST_3DLength_Spheroid' => array(
    ),
    'ST_Length3d_Spheroid' => array(
    ),*/
    'ST_LongestLine' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 100)'), ST_GeomFromText('LINESTRING(20 80, 98 190, 110 180, 50 75)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(100 100,98 190)',
                    ],
                ],
            ],
        ],
    ],
    'ST_OrderingEquals' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 10 10)'), ST_GeomFromText('LINESTRING(0 0, 10 10)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Overlaps' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(1 0.5)'), 3), ST_Buffer(ST_GeomFromText('LINESTRING(1 0, 1 1, 3 5)'), 0.5)) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Perimeter' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))', 2249)) AS value",
                    'result' => [
                        'value' => 122.630744000095,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('MULTIPOLYGON(((-71.1044543107478 42.340674480411,-71.1044542869917 42.3406744369506,-71.1044553562977 42.340673886454,-71.1044543107478 42.340674480411)),((-71.1044543107478 42.340674480411,-71.1044860600303 42.3407237015564,-71.1045215770124 42.3407653385914,-71.1045498002983 42.3407946553165,-71.1045611902745 42.3408058316308,-71.1046016507427 42.340837442371,-71.104617893173 42.3408475056957,-71.1048586153981 42.3409875993595,-71.1048736143677 42.3409959528211,-71.1048878050242 42.3410084812078,-71.1044020965803 42.3414730072048,-71.1039672113619 42.3412202916693,-71.1037740497748 42.3410666421308,-71.1044280218456 42.3406894151355,-71.1044543107478 42.340674480411)))'), false) AS value",
                    'result' => [
                        'value' => 257.412311446337,
                    ],
                ],
            ],
        ],
    ],
    /*'ST_Perimeter2D' => array(
    ),
    'ST_3DPerimeter' => array(
    ),*/
    'ST_PointOnSurface' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(0 5, 0 10)'))) AS value",
                    'result' => [
                        'value' => 'POINT(0 5)',
                    ],
                ],
            ],
        ],
    ],
    'ST_Project' => [
        'required_arguments' => 3,
        'total_arguments' => 3,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0', 'postgis-3.1', 'postgis-3.2', 'postgis-3.3'],
                    'sql' => "SELECT ST_X(ST_GeomFromText(ST_AsText({function}(ST_GeomFromText('POINT(0 0)'), 100000, 0.785398163397448)))) as value1, ST_Y(ST_GeomFromText(ST_AsText({function}(ST_GeomFromText('POINT(0 0)'), 100000, 0.785398163397448)))) AS value2",
                    'result' => [
                        'value1' => 0.635231029125537,
                        'value2' => 0.639472334729198,
                    ],
                ],
                [
                    'groups' => ['postgis-3.4'],
                    'sql' => "SELECT ST_X(ST_GeomFromText(ST_AsText({function}(ST_GeomFromText('POINT(0 0)'), 100000, 0.785398163397448)))) as value1, ST_Y(ST_GeomFromText(ST_AsText({function}(ST_GeomFromText('POINT(0 0)'), 100000, 0.785398163397448)))) AS value2",
                    'result' => [
                        'value1' => 70710.67811865476,
                        'value2' => 70710.67811865475,
                    ],
                ],
            ],
        ],
    ],
    'ST_Relate' => [
        'required_arguments' => 2,
        'total_arguments' => 3,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeometryFromText('POINT(1 2)'), ST_Buffer(ST_GeometryFromText('POINT(1 2)'),2), '0FFFFF212') AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    /*'ST_RelateMatch' => array(
    ),*/
    'ST_ShortestLine' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 100)'), ST_GeomFromText('LINESTRING(20 80, 98 190, 110 180, 50 75)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(100 100,73.0769230769231 115.384615384615)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 100)'), ST_GeomFromText('LINESTRING(20 80, 98 190, 110 180, 50 75)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(100 100,73.07692307692307 115.38461538461539)',
                    ],
                ],
            ],
        ],
    ],
    'ST_Touches' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1, 0 2)'), ST_GeomFromText('POINT(0 2)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_Within' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_Buffer(ST_GeomFromText('POINT(50 50)'), 20), ST_Buffer(ST_GeomFromText('POINT(50 50)'), 40)) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
];
