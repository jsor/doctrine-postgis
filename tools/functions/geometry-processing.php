<?php
/**
 * Geometry Processing
 * http://postgis.net/docs/reference.html#Geometry_Processing
 */
return array(
    'ST_Buffer' => array(
        'required_arguments' => 2,
        'total_arguments' => 3,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(100 90)'), 50, 'quad_segs=8'))",
                    'result' => array(
                        1 => 'POLYGON((150 90,149.039264020162 80.2454838991936,146.193976625564 70.8658283817455,141.573480615127 62.2214883490199,135.355339059327 54.6446609406727,127.77851165098 48.4265193848728,119.134171618255 43.8060233744357,109.754516100806 40.9607359798385,100 40,90.2454838991937 40.9607359798385,80.8658283817456 43.8060233744356,72.22148834902 48.4265193848727,64.6446609406727 54.6446609406725,58.4265193848728 62.2214883490198,53.8060233744357 70.8658283817454,50.9607359798385 80.2454838991934,50 89.9999999999998,50.9607359798384 99.7545161008062,53.8060233744356 109.134171618254,58.4265193848726 117.77851165098,64.6446609406725 125.355339059327,72.2214883490197 131.573480615127,80.8658283817453 136.193976625564,90.2454838991934 139.039264020161,99.9999999999998 140,109.754516100806 139.039264020162,119.134171618254 136.193976625564,127.77851165098 131.573480615127,135.355339059327 125.355339059327,141.573480615127 117.77851165098,146.193976625564 109.134171618255,149.039264020162 99.7545161008065,150 90))'
                    )
                ),
                array(
                    'sql' => "SELECT ST_NPoints({function}(ST_GeomFromText('POINT(100 90)'), 50)) As promisingcircle_pcount, ST_NPoints(ST_Buffer(ST_GeomFromText('POINT(100 90)'), 50, 2)) As lamecircle_pcount",
                    'result' => array(
                        'promisingcircle_pcount' => 33,
                        'lamecircle_pcount' => 9
                    ),
                )
            )
        )
    ),
    /*'ST_BuildArea' => array(
    ),*/
    'ST_Collect' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)') ))",
                    'result' => array(
                        1 => 'MULTIPOINT(1 2,-2 3)'
                    )
                )
            )
        )
    ),
    /*'ST_ConcaveHull' => array(
    ),
    'ST_ConvexHull' => array(
    ),
    'ST_CurveToLine' => array(
    ),
    'ST_DelaunayTriangles' => array(
    ),*/
    'ST_Difference' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(50 100, 50 200)'), ST_GeomFromText('LINESTRING(50 50, 50 150)')))",
                    'result' => array(
                        1 => 'LINESTRING(50 150,50 200)'
                    )
                )
            )
        )
    ),
    /*'ST_Dump' => array(
    ),
    'ST_DumpPoints' => array(
    ),
    'ST_DumpRings' => array(
    ),*/
    'ST_FlipCoordinates' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('POINT(1 2)')))",
                    'result' => array(
                        1 => 'POINT(2 1)'
                    )
                )
            )
        )
    ),
    'ST_Intersection' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(0 0)'), ST_GeomFromText('LINESTRING (0 0, 0 2)')))",
                    'result' => array(
                        1 => 'POINT(0 0)'
                    )
                )
            )
        )
    ),
    /*'ST_LineToCurve' => array(
    ),
    'ST_MakeValid' => array(
    ),
    'ST_MemUnion' => array(
    ),*/
    'ST_MinimumBoundingCircle' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_Collect(ST_GeomFromEWKT('LINESTRING(55 75,125 150)'), ST_Point(20, 80)), 8))",
                    'result' => array(
                        1 => 'POLYGON((135.59714732062 115,134.384753327498 102.690357210921,130.79416296937 90.8537670908995,124.963360620072 79.9451031602111,117.116420743937 70.3835792560632,107.554896839789 62.5366393799277,96.6462329091006 56.70583703063,84.8096427890789 53.115246672502,72.5000000000001 51.9028526793802,60.1903572109213 53.1152466725019,48.3537670908996 56.7058370306299,37.4451031602112 62.5366393799276,27.8835792560632 70.383579256063,20.0366393799278 79.9451031602109,14.20583703063 90.8537670908993,10.615246672502 102.690357210921,9.40285267938019 115,10.6152466725019 127.309642789079,14.2058370306299 139.1462329091,20.0366393799275 150.054896839789,27.883579256063 159.616420743937,37.4451031602109 167.463360620072,48.3537670908992 173.29416296937,60.190357210921 176.884753327498,72.4999999999998 178.09714732062,84.8096427890786 176.884753327498,96.6462329091003 173.29416296937,107.554896839789 167.463360620072,117.116420743937 159.616420743937,124.963360620072 150.054896839789,130.79416296937 139.146232909101,134.384753327498 127.309642789079,135.59714732062 115))'
                    )
                )
            )
        )
    ),
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
    'ST_Shift_Longitude' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(-118.58 38.38, -118.20 38.45)')))",
                    'result' => array(
                        1 => 'LINESTRING(241.42 38.38,241.8 38.45)'
                    )
                )
            )
        )
    ),
    /*'ST_Simplify' => array(
    ),
    'ST_SimplifyPreserveTopology' => array(
    ),*/
    'ST_Split' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.1',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_Buffer(ST_GeomFromText('POINT(100 90)'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190))))",
                    'result' => array(
                        1 => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.039264020162 80.2454838991936,146.193976625564 70.8658283817455,141.573480615127 62.2214883490199,135.355339059327 54.6446609406727,127.77851165098 48.4265193848728,119.134171618255 43.8060233744357,109.754516100806 40.9607359798385,100 40,90.2454838991937 40.9607359798385,80.8658283817456 43.8060233744356,72.22148834902 48.4265193848727,64.6446609406727 54.6446609406725,60.1371179574584 60.1371179574584,129.862882042542 129.862882042542,135.355339059327 125.355339059327,141.573480615127 117.77851165098,146.193976625564 109.134171618255,149.039264020162 99.7545161008065,150 90)),POLYGON((60.1371179574584 60.1371179574584,58.4265193848728 62.2214883490198,53.8060233744357 70.8658283817454,50.9607359798385 80.2454838991934,50 89.9999999999998,50.9607359798384 99.7545161008062,53.8060233744356 109.134171618254,58.4265193848726 117.77851165098,64.6446609406725 125.355339059327,72.2214883490197 131.573480615127,80.8658283817453 136.193976625564,90.2454838991934 139.039264020161,99.9999999999998 140,109.754516100806 139.039264020162,119.134171618254 136.193976625564,127.77851165098 131.573480615127,129.862882042542 129.862882042542,60.1371179574584 60.1371179574584)))'
                    )
                )
            )
        )
    ),
    'ST_SymDifference' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(50 100, 50 200)'), ST_GeomFromText('LINESTRING(50 50, 50 150)')))",
                    'result' => array(
                        1 => 'MULTILINESTRING((50 150,50 200),(50 50,50 100))'
                    )
                )
            )
        )
    ),
    'ST_Union' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 2)'), ST_GeomFromText('POINT(-2 3)')))",
                    'result' => array(
                        1 => 'MULTIPOINT(1 2,-2 3)'
                    )
                )
            )
        )
    ),
    /*'ST_UnaryUnion' => array(
    ),*/
);
