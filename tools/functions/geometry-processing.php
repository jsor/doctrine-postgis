<?php

declare(strict_types=1);

/**
 * Geometry Processing
 * http://postgis.net/docs/reference.html#Geometry_Processing.
 */
return [
    'ST_Buffer' => [
        'required_arguments' => 2,
        'total_arguments' => 3,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.2', 'postgis-3.4'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 90)'), 50, 'quad_segs=8')) AS value",
                    'result' => [
                        'value' => 'POLYGON((150 90,149.0392640201615 80.24548389919359,146.19397662556435 70.86582838174552,141.57348061512727 62.22148834901989,135.35533905932738 54.64466094067263,127.77851165098011 48.42651938487274,119.1341716182545 43.80602337443566,109.75451610080641 40.960735979838475,100 40,90.24548389919359 40.960735979838475,80.86582838174552 43.80602337443566,72.2214883490199 48.426519384872734,64.64466094067262 54.64466094067262,58.426519384872734 62.22148834901989,53.80602337443566 70.8658283817455,50.960735979838475 80.24548389919357,50 90,50.960735979838475 99.75451610080641,53.80602337443566 109.13417161825448,58.42651938487273 117.7785116509801,64.64466094067262 125.35533905932738,72.22148834901989 131.57348061512727,80.86582838174549 136.19397662556432,90.24548389919357 139.0392640201615,99.99999999999999 140,109.75451610080641 139.0392640201615,119.1341716182545 136.19397662556435,127.7785116509801 131.57348061512727,135.35533905932738 125.35533905932738,141.57348061512727 117.77851165098011,146.19397662556432 109.13417161825453,149.0392640201615 99.75451610080644,150 90))',
                    ],
                ],
                [
                    'groups' => ['postgis-3.6'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 90)'), 50, 'quad_segs=8')) AS value",
                    'result' => [
                        'value' => 'POLYGON((150 90,149.0392640201615 80.24548389919359,146.19397662556435 70.86582838174552,141.57348061512727 62.22148834901989,135.35533905932738 54.64466094067263,127.77851165098011 48.42651938487274,119.1341716182545 43.80602337443566,109.75451610080641 40.960735979838475,100 40,90.24548389919359 40.960735979838475,80.86582838174552 43.80602337443566,72.2214883490199 48.426519384872734,64.64466094067262 54.64466094067262,58.426519384872734 62.22148834901989,53.80602337443566 70.8658283817455,50.960735979838475 80.24548389919357,50 90,50.960735979838475 99.75451610080641,53.80602337443566 109.13417161825448,58.42651938487273 117.7785116509801,64.64466094067262 125.35533905932738,72.22148834901989 131.57348061512727,80.86582838174549 136.19397662556432,90.24548389919357 139.0392640201615,100 140,109.75451610080641 139.0392640201615,119.1341716182545 136.19397662556435,127.7785116509801 131.57348061512727,135.35533905932738 125.35533905932738,141.57348061512727 117.77851165098011,146.19397662556432 109.13417161825453,149.0392640201615 99.75451610080644,150 90))',
                    ],
                ],
                [
                    'sql' => "SELECT ST_NPoints({function}(ST_GeomFromText('POINT(100 90)'), 50)) AS promisingcircle_pcount, ST_NPoints(ST_Buffer(ST_GeomFromText('POINT(100 90)'), 50, 2)) AS lamecircle_pcount",
                    'result' => [
                        'promisingcircle_pcount' => 33,
                        'lamecircle_pcount' => 9,
                    ],
                ],
            ],
        ],
    ],
    /*'ST_BuildArea' => array(
    ),*/
    'ST_Collect' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.2'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)') )) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT(1 2,-2 3)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.4', 'postgis-3.6'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)') )) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT((1 2),(-2 3))',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_ConcaveHull' => array(
    ),
    'ST_ConvexHull' => array(
    ),
    'ST_CurveToLine' => array(
    ),
    'ST_DelaunayTriangles' => array(
    ),
    // TODO: Add ST_Subdivide - Divides geometry into smaller parts (critical for performance)
    // 'ST_Subdivide' => [
    //     'required_arguments' => 1,
    //     'total_arguments' => 2,
    // ],
    // TODO: Add ST_Clip - Clips geometry to a rectangle
    // 'ST_Clip' => [
    //     'required_arguments' => 2,
    //     'total_arguments' => 2,
    // ],
    // TODO: Add ST_ClipByBox2D - Clips geometry to a box2d
    // 'ST_ClipByBox2D' => [
    //     'required_arguments' => 2,
    //     'total_arguments' => 2,
    // ],
    // TODO: Add ST_VoronoiPolygons - Voronoi diagram (polygons)
    // 'ST_VoronoiPolygons' => [
    //     'required_arguments' => 1,
    //     'total_arguments' => 3,
    // ],
    // TODO: Add ST_VoronoiLines - Voronoi diagram (lines)
    // 'ST_VoronoiLines' => [
    //     'required_arguments' => 1,
    //     'total_arguments' => 3,
    // ],
    */
    'ST_Difference' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(50 100, 50 200)'), ST_GeomFromText('LINESTRING(50 50, 50 150)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(50 150,50 200)',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_Dump' => array(
    ),
    'ST_DumpPoints' => array(
    ),
    'ST_DumpRings' => array(
    ),*/
    'ST_FlipCoordinates' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('POINT(1 2)'))) AS value",
                    'result' => [
                        'value' => 'POINT(2 1)',
                    ],
                ],
            ],
        ],
    ],
    'ST_Intersection' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(0 0)'), ST_GeomFromText('LINESTRING (0 0, 0 2)'))) AS value",
                    'result' => [
                        'value' => 'POINT(0 0)',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_LineToCurve' => array(
    ),
    'ST_MakeValid' => array(
    ),
    'ST_MemUnion' => array(
    ),*/
    'ST_MinimumBoundingCircle' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromEWKT('MULTIPOINT((10 10), (20 20), (10 20), (15 19))'), 2)) AS value",
                    'result' => [
                        'value' => 'POLYGON((15 22.653668647301796,20.411961001461968 20.41196100146197,22.653668647301796 15,20.41196100146197 9.58803899853803,15.000000000000002 7.346331352698204,9.58803899853803 9.588038998538028,7.346331352698204 14.999999999999998,9.588038998538028 20.411961001461968,14.999999999999998 22.653668647301796))',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_Polygonize' => array(
    ),
    'ST_Node' => array(
    ),
    'ST_OffsetCurve' => array(
    ),
    'ST_RemoveRepeatedPoints' => array(
    ),
    'ST_SharedPaths' => array(
    ),*/
    'ST_ShiftLongitude' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(-118.58 38.38, -118.20 38.45)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(241.42000000000002 38.38,241.8 38.45)',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_Simplify' => array(
    ),
    'ST_SimplifyPreserveTopology' => array(
    ),
    // TODO: Add ST_LineMerge - Merges linestrings
    // 'ST_LineMerge' => [
    //     'required_arguments' => 1,
    //     'total_arguments' => 1,
    // ],
    // TODO: Add ST_CollectionExtract - Extracts elements from a collection
    // 'ST_CollectionExtract' => [
    //     'required_arguments' => 1,
    //     'total_arguments' => 2,
    // ],
    // TODO: Add ST_CollectionHomogenize - Homogenizes a collection
    // 'ST_CollectionHomogenize' => [
    //     'required_arguments' => 1,
    //     'total_arguments' => 1,
    // ],
    */
    'ST_Split' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.2', 'postgis-3.4'],
                    'sql' => "SELECT ST_AsText({function}(ST_Buffer(ST_GeomFromText('POINT(100 90)'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value",
                    'result' => [
                        'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.0392640201615 80.24548389919359,146.19397662556435 70.86582838174552,141.57348061512727 62.22148834901989,135.35533905932738 54.64466094067263,127.77851165098011 48.42651938487274,119.1341716182545 43.80602337443566,109.75451610080641 40.960735979838475,100 40,90.24548389919359 40.960735979838475,80.86582838174552 43.80602337443566,72.2214883490199 48.426519384872734,64.64466094067262 54.64466094067262,60.13711795745844 60.13711795745844,129.86288204254154 129.86288204254154,135.35533905932738 125.35533905932738,141.57348061512727 117.77851165098011,146.19397662556432 109.13417161825453,149.0392640201615 99.75451610080644,150 90)),POLYGON((60.13711795745844 60.13711795745844,58.426519384872734 62.22148834901989,53.80602337443566 70.8658283817455,50.960735979838475 80.24548389919357,50 90,50.960735979838475 99.75451610080641,53.80602337443566 109.13417161825448,58.42651938487273 117.7785116509801,64.64466094067262 125.35533905932738,72.22148834901989 131.57348061512727,80.86582838174549 136.19397662556432,90.24548389919357 139.0392640201615,99.99999999999999 140,109.75451610080641 139.0392640201615,119.1341716182545 136.19397662556435,127.7785116509801 131.57348061512727,129.86288204254154 129.86288204254154,60.13711795745844 60.13711795745844)))',
                    ],
                ],
                [
                    'groups' => ['postgis-3.6'],
                    'sql' => "SELECT ST_AsText({function}(ST_Buffer(ST_GeomFromText('POINT(100 90)'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value",
                    'result' => [
                        'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.0392640201615 80.24548389919359,146.19397662556435 70.86582838174552,141.57348061512727 62.22148834901989,135.35533905932738 54.64466094067263,127.77851165098011 48.42651938487274,119.1341716182545 43.80602337443566,109.75451610080641 40.960735979838475,100 40,90.24548389919359 40.960735979838475,80.86582838174552 43.80602337443566,72.2214883490199 48.426519384872734,64.64466094067262 54.64466094067262,60.13711795745844 60.13711795745844,129.86288204254154 129.86288204254154,135.35533905932738 125.35533905932738,141.57348061512727 117.77851165098011,146.19397662556432 109.13417161825453,149.0392640201615 99.75451610080644,150 90)),POLYGON((60.13711795745844 60.13711795745844,58.426519384872734 62.22148834901989,53.80602337443566 70.8658283817455,50.960735979838475 80.24548389919357,50 90,50.960735979838475 99.75451610080641,53.80602337443566 109.13417161825448,58.42651938487273 117.7785116509801,64.64466094067262 125.35533905932738,72.22148834901989 131.57348061512727,80.86582838174549 136.19397662556432,90.24548389919357 139.0392640201615,100 140,109.75451610080641 139.0392640201615,119.1341716182545 136.19397662556435,127.7785116509801 131.57348061512727,129.86288204254154 129.86288204254154,60.13711795745844 60.13711795745844)))',
                    ],
                ],
            ],
        ],
    ],
    'ST_SymDifference' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(50 100, 50 200)'), ST_GeomFromText('LINESTRING(50 50, 50 150)'))) AS value",
                    'result' => [
                        'value' => 'MULTILINESTRING((50 150,50 200),(50 50,50 100))',
                    ],
                ],
            ],
        ],
    ],
    'ST_Union' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.2'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)'))) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT(1 2,-2 3)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.4', 'postgis-3.6'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)'))) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT((1 2),(-2 3))',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_UnaryUnion' => array(
    ),*/
];
