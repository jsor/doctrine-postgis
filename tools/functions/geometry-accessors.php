<?php
/**
 * Geometry Accessors
 * http://postgis.net/docs/reference.html#Geometry_Accessors
 */
return array(
    'GeometryType' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)'))",
                    'result' => array(
                        1 => 'LINESTRING'
                    )
                ),
            )
        )
    ),
    'ST_Boundary' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(1 1,0 0, -1 1)')))",
                    'result' => array(
                        1 => 'MULTIPOINT(1 1,-1 1)'
                    )
                ),
            )
        )
    ),
    'ST_CoordDim' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(1 1,0 0, -1 1)'))",
                    'result' => array(
                        1 => 2
                    )
                ),
            )
        )
    ),
    'ST_Dimension' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('GEOMETRYCOLLECTION(LINESTRING(1 1,0 0),POINT(0 0))')",
                    'result' => array(
                        1 => 1
                    )
                ),
            )
        )
    ),
    'ST_EndPoint' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(1 1, 2 2, 3 3)')))",
                    'result' => array(
                        1 => 'POINT(3 3)'
                    )
                ),
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 1)')))",
                    'result' => array(
                        1 => null
                    )
                ),
            )
        )
    ),
    'ST_Envelope' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(0 0, 1 3)')))",
                    'result' => array(
                        1 => 'POLYGON((0 0,0 3,1 3,1 0,0 0))'
                    )
                ),
            )
        )
    ),
    'ST_ExteriorRing' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYGON((0 0 1, 1 1 1, 1 2 1, 1 1 1, 0 0 1))')))",
                    'result' => array(
                        1 => 'LINESTRING(0 0 1,1 1 1,1 2 1,1 1 1,0 0 1)'
                    )
                ),
            )
        )
    ),
    'ST_GeometryN' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('MULTILINESTRING((0 0 1, 1 1 1, 1 2 1, 1 1 1, 0 0 1))'), 1))",
                    'result' => array(
                        1 => 'LINESTRING(0 0 1,1 1 1,1 2 1,1 1 1,0 0 1)'
                    )
                ),
            )
        )
    ),
    'ST_GeometryType' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)'))",
                    'result' => array(
                        1 => 'ST_LineString'
                    )
                ),
            )
        )
    ),
    'ST_InteriorRingN' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0),(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07))'), 1))",
                    'result' => array(
                        1 => 'LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)'
                    )
                ),
                // Out of range
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0),(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07))'), 3))",
                    'result' => array(
                        1 => null
                    )
                ),
            )
        )
    ),
    'ST_IsClosed' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)'))",
                    'result' => array(
                        1 => false
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 0 1, 1 1, 0 0)'))",
                    'result' => array(
                        1 => true
                    )
                ),
            )
        )
    ),
    'ST_IsCollection' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'group' => 'postgis-2.x',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)'))",
                    'result' => array(
                        1 => false
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('MULTIPOINT((0 0))'))",
                    'result' => array(
                        1 => true
                    )
                ),
            )
        )
    ),
    'ST_IsEmpty' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('GEOMETRYCOLLECTION EMPTY'))",
                    'result' => array(
                        1 => true
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((1 2, 3 4, 5 6, 1 2))'))",
                    'result' => array(
                        1 => false
                    )
                ),
            )
        )
    ),
    'ST_IsRing' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)'))",
                    'result' => array(
                        1 => false
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 0 1, 1 1, 1 0, 0 0)'))",
                    'result' => array(
                        1 => true
                    )
                ),
            )
        )
    ),
    'ST_IsSimple' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(1 1,2 2,2 3.5,1 3,1 2,2 1)'))",
                    'result' => array(
                        1 => false
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((1 2, 3 4, 5 6, 1 2))'))",
                    'result' => array(
                        1 => true
                    )
                ),
            )
        )
    ),
    'ST_IsValid' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0))'))",
                    'result' => array(
                        1 => false
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)'))",
                    'result' => array(
                        1 => true
                    )
                ),
                array(
                    'group' => 'postgis-2.x',
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)'), 1)",
                    'result' => array(
                        1 => true
                    )
                ),
            )
        )
    ),
    'ST_IsValidReason' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(220227 150406,2220227 150407,222020 150410)'))",
                    'result' => array(
                        1 => 'Valid Geometry'
                    )
                ),
                array(
                    'group' => 'postgis-2.x',
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(220227 150406,2220227 150407,222020 150410)'), 1)",
                    'result' => array(
                        1 => 'Valid Geometry'
                    )
                ),
            )
        )
    ),
    'ST_IsValidDetail' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.x',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(1 1, 1 1)'))",
                    'result' => array(
                        1 => '(f,"Too few points in geometry component",0101000000000000000000F03F000000000000F03F)'
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(220227 150406,2220227 150407,222020 150410)'), 1)",
                    'result' => array(
                        1 => '(t,,)'
                    )
                ),
            )
        )
    ),
    'ST_M' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 2 3 4)'))",
                    'result' => array(
                        1 => 4
                    )
                ),
            )
        )
    ),
    'ST_NDims' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(1 1)'))",
                    'result' => array(
                        1 => 2
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINTM(1 1 0.5)'))",
                    'result' => array(
                        1 => 3
                    )
                ),
            )
        )
    ),
    'ST_NPoints' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)'))",
                    'result' => array(
                        1 => 4
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('LINESTRING(77.29 29.07 1,77.42 29.26 0,77.27 29.31 -1,77.29 29.07 3)'))",
                    'result' => array(
                        1 => 4
                    )
                ),
            )
        )
    ),
    'ST_NRings' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((1 2, 3 4, 5 6, 1 2))'))",
                    'result' => array(
                        1 => 1
                    )
                ),
            )
        )
    ),
    'ST_NumGeometries' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                // PostGIS 1.5: ST_NumGeometries returns NULL for single geometries
                array(
                    'group' => 'postgis-1.5',
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)'))",
                    'result' => array(
                        1 => null
                    )
                ),
                // PostGIS 2.x: ST_NumGeometries returns NULL for single geometries
                array(
                    'group' => 'postgis-2.x',
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)'))",
                    'result' => array(
                        1 => 1
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('GEOMETRYCOLLECTION(MULTIPOINT(-2 3 , -2 2),LINESTRING(5 5 ,10 10),POLYGON((-7 4.2,-7.1 5,-7.1 4.3,-7 4.2)))'))",
                    'result' => array(
                        1 => 3
                    )
                ),
            )
        )
    ),
    'ST_NumInteriorRings' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((-7 4.2,-7.1 5,-7.1 4.3,-7 4.2),(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07))'))",
                    'result' => array(
                        1 => 1
                    )
                ),
            )
        )
    ),
    'ST_NumInteriorRing' => array(
        'alias_for' => 'ST_NumInteriorRings'
    ),
    'ST_NumPatches' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'group' => 'postgis-2.x',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYHEDRALSURFACE( ((0 0 0, 0 0 1, 0 1 1, 0 1 0, 0 0 0)), ((0 0 0, 0 1 0, 1 1 0, 1 0 0, 0 0 0)), ((0 0 0, 1 0 0, 1 0 1, 0 0 1, 0 0 0)), ((1 1 0, 1 1 1, 1 0 1, 1 0 0, 1 1 0)), ((0 1 0, 0 1 1, 1 1 1, 1 1 0, 0 1 0)), ((0 0 1, 1 0 1, 1 1 1, 0 1 1, 0 0 1)) )'))",
                    'result' => array(
                        1 => 6
                    )
                ),
            )
        )
    ),
    'ST_NumPoints' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)'))",
                    'result' => array(
                        1 => 4
                    )
                ),
            )
        )
    ),
    'ST_PatchN' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.x',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYHEDRALSURFACE( ((0 0 0, 0 0 1, 0 1 1, 0 1 0, 0 0 0)), ((0 0 0, 0 1 0, 1 1 0, 1 0 0, 0 0 0)), ((0 0 0, 1 0 0, 1 0 1, 0 0 1, 0 0 0)), ((1 1 0, 1 1 1, 1 0 1, 1 0 0, 1 1 0)), ((0 1 0, 0 1 1, 1 1 1, 1 1 0, 0 1 0)), ((0 0 1, 1 0 1, 1 1 1, 0 1 1, 0 0 1)) )'), 2))",
                    'result' => array(
                        1 => 'POLYGON((0 0 0,0 1 0,1 1 0,1 0 0,0 0 0))'
                    )
                ),
            )
        )
    ),
    'ST_PointN' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('LINESTRING(0 0, 1 1, 2 2)'), 2))",
                    'result' => array(
                        1 => 'POINT(1 1)'
                    )
                ),
            )
        )
    ),
    'ST_SRID' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(-71.1043 42.315)',4326))",
                    'result' => array(
                        1 => 4326
                    )
                ),
            )
        )
    ),
    'ST_StartPoint' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(0 1, 0 2)')))",
                    'result' => array(
                        1 => 'POINT(0 1)'
                    )
                ),
            )
        )
    ),
    'ST_Summary' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0))'))",
                    'result' => array(
                        1 => 'Polygon[B] with 1 rings
   ring 0 has 5 points'
                    )
                ),
            )
        )
    ),
    'ST_X' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1.5 2 3 4)'))",
                    'result' => array(
                        1 => 1.5
                    )
                ),
            )
        )
    ),
    'ST_XMax' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)')",
                    'result' => array(
                        1 => 4
                    )
                ),
            )
        )
    ),
    'ST_XMin' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)')",
                    'result' => array(
                        1 => 1
                    )
                ),
            )
        )
    ),
    'ST_Y' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1.5 2 3 4)'))",
                    'result' => array(
                        1 => 2
                    )
                ),
            )
        )
    ),
    'ST_YMax' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)')",
                    'result' => array(
                        1 => 5
                    )
                ),
            )
        )
    ),
    'ST_YMin' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)')",
                    'result' => array(
                        1 => 2
                    )
                ),
            )
        )
    ),
    'ST_Z' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1.5 2 3 4)'))",
                    'result' => array(
                        1 => 3
                    )
                ),
            )
        )
    ),
    'ST_ZMax' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)')",
                    'result' => array(
                        1 => 6
                    )
                ),
            )
        )
    ),
    'ST_Zmflag' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('LINESTRING(1 2, 3 4)'))",
                    'result' => array(
                        1 => 0
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 2 3 4)'))",
                    'result' => array(
                        1 => 3
                    )
                ),
            )
        )
    ),
    'ST_ZMin' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)')",
                    'result' => array(
                        1 => 3
                    )
                ),
            )
        )
    ),
);
