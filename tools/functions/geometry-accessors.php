<?php

declare(strict_types=1);

/**
 * Geometry Accessors
 * http://postgis.net/docs/reference.html#Geometry_Accessors.
 */
return [
    'GeometryType' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)')) AS value",
                    'result' => [
                        'value' => 'LINESTRING',
                    ],
                ],
            ],
        ],
    ],
    'ST_Boundary' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0', 'postgis-3.1', 'postgis-3.2'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(1 1,0 0, -1 1)'))) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT(1 1,-1 1)',
                    ],
                ],
                [
                    'groups' => ['postgis-3.3'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(1 1,0 0, -1 1)'))) AS value",
                    'result' => [
                        'value' => 'MULTIPOINT((1 1),(-1 1))',
                    ],
                ],
            ],
        ],
    ],
    'ST_CoordDim' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(1 1,0 0, -1 1)')) AS value",
                    'result' => [
                        'value' => 2,
                    ],
                ],
            ],
        ],
    ],
    'ST_Dimension' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}('GEOMETRYCOLLECTION(LINESTRING(1 1,0 0),POINT(0 0))') AS value",
                    'result' => [
                        'value' => 1,
                    ],
                ],
            ],
        ],
    ],
    'ST_EndPoint' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(1 1, 2 2, 3 3)'))) AS value",
                    'result' => [
                        'value' => 'POINT(3 3)',
                    ],
                ],
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(1 1)'))) AS value",
                    'result' => [
                        'value' => null,
                    ],
                ],
            ],
        ],
    ],
    'ST_Envelope' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(0 0, 1 3)'))) AS value",
                    'result' => [
                        'value' => 'POLYGON((0 0,0 3,1 3,1 0,0 0))',
                    ],
                ],
            ],
        ],
    ],
    'ST_ExteriorRing' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYGON((0 0 1, 1 1 1, 1 2 1, 1 1 1, 0 0 1))'))) AS value",
                    'result' => [
                        'value' => 'LINESTRING(0 0 1,1 1 1,1 2 1,1 1 1,0 0 1)',
                    ],
                ],
            ],
        ],
    ],
    'ST_GeometryN' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('MULTILINESTRING((0 0 1, 1 1 1, 1 2 1, 1 1 1, 0 0 1))'), 1)) AS value",
                    'result' => [
                        'value' => 'LINESTRING(0 0 1,1 1 1,1 2 1,1 1 1,0 0 1)',
                    ],
                ],
            ],
        ],
    ],
    'ST_GeometryType' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)')) AS value",
                    'result' => [
                        'value' => 'ST_LineString',
                    ],
                ],
            ],
        ],
    ],
    'ST_InteriorRingN' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0),(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07))'), 1)) AS value",
                    'result' => [
                        'value' => 'LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)',
                    ],
                ],
                // Out of range
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0),(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07))'), 3)) AS value",
                    'result' => [
                        'value' => null,
                    ],
                ],
            ],
        ],
    ],
    'ST_IsClosed' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 0 1, 1 1, 0 0)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_IsCollection' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('MULTIPOINT((0 0))')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_IsEmpty' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('GEOMETRYCOLLECTION EMPTY')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((1 2, 3 4, 5 6, 1 2))')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
            ],
        ],
    ],
    'ST_IsRing' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 0 1, 1 1, 1 0, 0 0)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_IsSimple' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(1 1,2 2,2 3.5,1 3,1 2,2 1)')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('Point(1 2)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_IsValid' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0))')) AS value",
                    'result' => [
                        'value' => false,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)')) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(0 0, 1 1)'), 1) AS value",
                    'result' => [
                        'value' => true,
                    ],
                ],
            ],
        ],
    ],
    'ST_IsValidReason' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(220227 150406,2220227 150407,222020 150410)')) AS value",
                    'result' => [
                        'value' => 'Valid Geometry',
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(220227 150406,2220227 150407,222020 150410)'), 1) AS value",
                    'result' => [
                        'value' => 'Valid Geometry',
                    ],
                ],
            ],
        ],
    ],
    'ST_IsValidDetail' => [
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(1 1, 1 1)')) AS value",
                    'result' => [
                        'value' => '(f,"Too few points in geometry component",0101000000000000000000F03F000000000000F03F)',
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(220227 150406,2220227 150407,222020 150410)'), 1) AS value",
                    'result' => [
                        'value' => '(t,,)',
                    ],
                ],
            ],
        ],
    ],
    'ST_M' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 2 3 4)')) AS value",
                    'result' => [
                        'value' => 4,
                    ],
                ],
            ],
        ],
    ],
    'ST_NDims' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(1 1)')) AS value",
                    'result' => [
                        'value' => 2,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINTM(1 1 0.5)')) AS value",
                    'result' => [
                        'value' => 3,
                    ],
                ],
            ],
        ],
    ],
    'ST_NPoints' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)')) AS value",
                    'result' => [
                        'value' => 4,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('LINESTRING(77.29 29.07 1,77.42 29.26 0,77.27 29.31 -1,77.29 29.07 3)')) AS value",
                    'result' => [
                        'value' => 4,
                    ],
                ],
            ],
        ],
    ],
    'ST_NRings' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((1 2, 3 4, 5 6, 1 2))')) AS value",
                    'result' => [
                        'value' => 1,
                    ],
                ],
            ],
        ],
    ],
    'ST_NumGeometries' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)')) AS value",
                    'result' => [
                        'value' => 1,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('GEOMETRYCOLLECTION(MULTIPOINT(-2 3 , -2 2),LINESTRING(5 5 ,10 10),POLYGON((-7 4.2,-7.1 5,-7.1 4.3,-7 4.2)))')) AS value",
                    'result' => [
                        'value' => 3,
                    ],
                ],
            ],
        ],
    ],
    'ST_NumInteriorRings' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((-7 4.2,-7.1 5,-7.1 4.3,-7 4.2),(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07))')) AS value",
                    'result' => [
                        'value' => 1,
                    ],
                ],
            ],
        ],
    ],
    'ST_NumInteriorRing' => [
        'alias_for' => 'ST_NumInteriorRings',
    ],
    'ST_NumPatches' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYHEDRALSURFACE( ((0 0 0, 0 0 1, 0 1 1, 0 1 0, 0 0 0)), ((0 0 0, 0 1 0, 1 1 0, 1 0 0, 0 0 0)), ((0 0 0, 1 0 0, 1 0 1, 0 0 1, 0 0 0)), ((1 1 0, 1 1 1, 1 0 1, 1 0 0, 1 1 0)), ((0 1 0, 0 1 1, 1 1 1, 1 1 0, 0 1 0)), ((0 0 1, 1 0 1, 1 1 1, 0 1 1, 0 0 1)) )')) AS value",
                    'result' => [
                        'value' => 6,
                    ],
                ],
            ],
        ],
    ],
    'ST_NumPoints' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)')) AS value",
                    'result' => [
                        'value' => 4,
                    ],
                ],
            ],
        ],
    ],
    'ST_PatchN' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('POLYHEDRALSURFACE( ((0 0 0, 0 0 1, 0 1 1, 0 1 0, 0 0 0)), ((0 0 0, 0 1 0, 1 1 0, 1 0 0, 0 0 0)), ((0 0 0, 1 0 0, 1 0 1, 0 0 1, 0 0 0)), ((1 1 0, 1 1 1, 1 0 1, 1 0 0, 1 1 0)), ((0 1 0, 0 1 1, 1 1 1, 1 1 0, 0 1 0)), ((0 0 1, 1 0 1, 1 1 1, 0 1 1, 0 0 1)) )'), 2)) AS value",
                    'result' => [
                        'value' => 'POLYGON((0 0 0,0 1 0,1 1 0,1 0 0,0 0 0))',
                    ],
                ],
            ],
        ],
    ],
    'ST_PointN' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromText('LINESTRING(0 0, 1 1, 2 2)'), 2)) AS value",
                    'result' => [
                        'value' => 'POINT(1 1)',
                    ],
                ],
            ],
        ],
    ],
    'ST_SRID' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POINT(-71.1043 42.315)',4326)) AS value",
                    'result' => [
                        'value' => 4326,
                    ],
                ],
            ],
        ],
    ],
    'ST_StartPoint' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(0 1, 0 2)'))) AS value",
                    'result' => [
                        'value' => 'POINT(0 1)',
                    ],
                ],
            ],
        ],
    ],
    'ST_Summary' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromText('POLYGON((0 0, 1 1, 1 2, 1 1, 0 0))')) AS value",
                    'result' => [
                        'value' => 'Polygon[B] with 1 ring:
   ring 0 has 5 points',
                    ],
                ],
            ],
        ],
    ],
    'ST_X' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1.5 2 3 4)')) AS value",
                    'result' => [
                        'value' => 1.5,
                    ],
                ],
            ],
        ],
    ],
    'ST_XMax' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)') AS value",
                    'result' => [
                        'value' => 4,
                    ],
                ],
            ],
        ],
    ],
    'ST_XMin' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)') AS value",
                    'result' => [
                        'value' => 1,
                    ],
                ],
            ],
        ],
    ],
    'ST_Y' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1.5 2 3 4)')) AS value",
                    'result' => [
                        'value' => 2,
                    ],
                ],
            ],
        ],
    ],
    'ST_YMax' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)') AS value",
                    'result' => [
                        'value' => 5,
                    ],
                ],
            ],
        ],
    ],
    'ST_YMin' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)') AS value",
                    'result' => [
                        'value' => 2,
                    ],
                ],
            ],
        ],
    ],
    'ST_Z' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1.5 2 3 4)')) AS value",
                    'result' => [
                        'value' => 3,
                    ],
                ],
            ],
        ],
    ],
    'ST_ZMax' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)') AS value",
                    'result' => [
                        'value' => 6,
                    ],
                ],
            ],
        ],
    ],
    'ST_Zmflag' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('LINESTRING(1 2, 3 4)')) AS value",
                    'result' => [
                        'value' => 0,
                    ],
                ],
                [
                    'sql' => "SELECT {function}(ST_GeomFromEWKT('POINT(1 2 3 4)')) AS value",
                    'result' => [
                        'value' => 3,
                    ],
                ],
            ],
        ],
    ],
    'ST_ZMin' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'return_type' => 'numeric',
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT {function}('BOX3D(1 2 3, 4 5 6)') AS value",
                    'result' => [
                        'value' => 3,
                    ],
                ],
            ],
        ],
    ],
];
