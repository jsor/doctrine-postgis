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
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 90)'), 50, 'quad_segs=8')) AS value",
                    'result' => [
                        'value' => 'POLYGON((150 90,149.039264020162 80.2454838991936,146.193976625564 70.8658283817455,141.573480615127 62.2214883490199,135.355339059327 54.6446609406727,127.77851165098 48.4265193848728,119.134171618255 43.8060233744357,109.754516100806 40.9607359798385,100 40,90.2454838991937 40.9607359798385,80.8658283817456 43.8060233744356,72.22148834902 48.4265193848727,64.6446609406727 54.6446609406725,58.4265193848728 62.2214883490198,53.8060233744357 70.8658283817454,50.9607359798385 80.2454838991934,50 89.9999999999998,50.9607359798384 99.7545161008062,53.8060233744356 109.134171618254,58.4265193848726 117.77851165098,64.6446609406725 125.355339059327,72.2214883490197 131.573480615127,80.8658283817453 136.193976625564,90.2454838991934 139.039264020161,99.9999999999998 140,109.754516100806 139.039264020162,119.134171618254 136.193976625564,127.77851165098 131.573480615127,135.355339059327 125.355339059327,141.573480615127 117.77851165098,146.193976625564 109.134171618255,149.039264020162 99.7545161008065,150 90))',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 90)'), 50, 'quad_segs=8')) AS value",
                    'result' => [
                        'value' => 'POLYGON((150 90,149.0392640201615 80.2454838991936,146.19397662556435 70.86582838174553,141.57348061512727 62.221488349019914,135.3553390593274 54.64466094067266,127.77851165098015 48.42651938487277,119.13417161825454 43.80602337443568,109.75451610080648 40.96073597983849,100.00000000000009 40,90.24548389919367 40.96073597983846,80.86582838174562 43.806023374435625,72.22148834901998 48.426519384872684,64.64466094067271 54.644660940672544,58.42651938487281 62.221488349019786,53.80602337443571 70.86582838174539,50.9607359798385 80.24548389919345,50 89.99999999999984,50.96073597983845 99.75451610080624,53.80602337443559 109.13417161825431,58.42651938487263 117.77851165097995,64.64466094067248 125.35533905932724,72.22148834901971 131.57348061512715,80.86582838174532 136.19397662556426,90.24548389919335 139.03926402016148,99.99999999999977 140,109.75451610080616 139.03926402016157,119.13417161825426 136.19397662556443,127.77851165097987 131.57348061512744,135.35533905932718 125.35533905932758,141.5734806151271 117.77851165098036,146.19397662556423 109.13417161825477,149.03926402016145 99.75451610080674,150 90))',
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
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)') )) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT(1 2,-2 3)',
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
    ),*/
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
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromEWKT('MULTIPOINT((10 10), (20 20), (10 20), (15 19))'), 2)) AS value",
                    'result' => [
                        'value' => 'POLYGON((15 22.6536686473018,20.411961001462 20.411961001462,22.6536686473018 15,20.411961001462 9.58803899853803,15 7.3463313526982,9.58803899853803 9.58803899853803,7.3463313526982 15,9.58803899853803 20.411961001462,15 22.6536686473018))',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
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
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(-118.58 38.38, -118.20 38.45)'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(241.42 38.38,241.8 38.45)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
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
    ),*/
    'ST_Split' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsText({function}(ST_Buffer(ST_GeomFromText('POINT(100 90)'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value",
                    'result' => [
                        'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.039264020162 80.2454838991936,146.193976625564 70.8658283817455,141.573480615127 62.2214883490199,135.355339059327 54.6446609406727,127.77851165098 48.4265193848728,119.134171618255 43.8060233744357,109.754516100806 40.9607359798385,100 40,90.2454838991937 40.9607359798385,80.8658283817456 43.8060233744356,72.22148834902 48.4265193848727,64.6446609406727 54.6446609406725,60.1371179574584 60.1371179574584,129.862882042542 129.862882042542,135.355339059327 125.355339059327,141.573480615127 117.77851165098,146.193976625564 109.134171618255,149.039264020162 99.7545161008065,150 90)),POLYGON((60.1371179574584 60.1371179574584,58.4265193848728 62.2214883490198,53.8060233744357 70.8658283817454,50.9607359798385 80.2454838991934,50 89.9999999999998,50.9607359798384 99.7545161008062,53.8060233744356 109.134171618254,58.4265193848726 117.77851165098,64.6446609406725 125.355339059327,72.2214883490197 131.573480615127,80.8658283817453 136.193976625564,90.2454838991934 139.039264020161,99.9999999999998 140,109.754516100806 139.039264020162,119.134171618254 136.193976625564,127.77851165098 131.573480615127,129.862882042542 129.862882042542,60.1371179574584 60.1371179574584)))',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
                    'sql' => "SELECT ST_AsText({function}(ST_Buffer(ST_GeomFromText('POINT(100 90)'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value",
                    'result' => [
                        'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.0392640201615 80.2454838991936,146.19397662556435 70.86582838174553,141.57348061512727 62.221488349019914,135.3553390593274 54.64466094067266,127.77851165098015 48.42651938487277,119.13417161825454 43.80602337443568,109.75451610080648 40.96073597983849,100.00000000000009 40,90.24548389919367 40.96073597983846,80.86582838174562 43.806023374435625,72.22148834901998 48.426519384872684,64.64466094067271 54.644660940672544,60.13711795745844 60.13711795745844,129.86288204254154 129.86288204254154,135.35533905932718 125.35533905932758,141.5734806151271 117.77851165098036,146.19397662556423 109.13417161825477,149.03926402016145 99.75451610080674,150 90)),POLYGON((60.13711795745844 60.13711795745844,58.42651938487281 62.221488349019786,53.80602337443571 70.86582838174539,50.9607359798385 80.24548389919345,50 89.99999999999984,50.96073597983845 99.75451610080624,53.80602337443559 109.13417161825431,58.42651938487263 117.77851165097995,64.64466094067248 125.35533905932724,72.22148834901971 131.57348061512715,80.86582838174532 136.19397662556426,90.24548389919335 139.03926402016148,99.99999999999977 140,109.75451610080616 139.03926402016157,119.13417161825426 136.19397662556443,127.77851165097987 131.57348061512744,129.86288204254154 129.86288204254154,60.13711795745844 60.13711795745844)))',
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
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)'))) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT(1 2,-2 3)',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_UnaryUnion' => array(
    ),*/
];
