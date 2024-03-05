<?php

declare(strict_types=1);

/**
 * Geometry Editors
 * http://postgis.net/docs/reference.html#Geometry_Editors.
 */
return [
    'ST_AddPoint' => [
        'required_arguments' => 2,
        'total_arguments' => 3,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(1.1115678 2.123, 4.111111 3.2374897, 4.11112 3.23748667)', 4326), ST_GeomFromText('POINT(-123.365556 48.428611)', 4326))) as value1, ST_AsText({function}(ST_GeomFromText('LINESTRING(1.1115678 2.123, 4.111111 3.2374897, 4.11112 3.23748667)', 4326), ST_GeomFromText('POINT(-123.365556 48.428611)', 4326), 1)) AS value2",
                    'result' => [
                        'value1' => 'LINESTRING(1.1115678 2.123,4.111111 3.2374897,4.11112 3.23748667,-123.365556 48.428611)',
                        'value2' => 'LINESTRING(1.1115678 2.123,-123.365556 48.428611,4.111111 3.2374897,4.11112 3.23748667)',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_Affine' => array(
    ),
    'ST_Force2D' => array(
    ),
    'ST_Force3D' => array(
    ),
    'ST_Force3DZ' => array(
    ),
    'ST_Force3DM' => array(
    ),
    'ST_Force4D' => array(
    ),
    'ST_ForceCollection' => array(
    ),
    'ST_ForceSFS' => array(
    ),
    'ST_ForceRHR' => array(
    ),
    'ST_LineMerge' => array(
    ),
    'ST_CollectionExtract' => array(
    ),
    'ST_CollectionHomogenize' => array(
    ),*/
    'ST_Multi' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))'))) AS value",
                    'result' => [
                        'value' => 'MULTIPOLYGON(((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416)))',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_RemovePoint' => array(
    ),
    'ST_Reverse' => array(
    ),
    'ST_Rotate' => array(
    ),
    'ST_RotateX' => array(
    ),
    'ST_RotateY' => array(
    ),
    'ST_RotateZ' => array(
    ),*/
    'ST_Scale' => [
        'required_arguments' => 3,
        'total_arguments' => 4,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING(1 2 3, 1 1 1)'), 0.5, 0.75, 0.8)) AS value",
                    'result' => [
                        'value' => 'LINESTRING(0.5 1.5 2.4,0.5 0.75 0.8)',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_Segmentize' => array(
    ),
    'ST_SetPoint' => array(
    ),*/
    'ST_SetSRID' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'sql' => 'SELECT ST_AsEWKT({function}(ST_Point(-123.365556, 48.428611),4326)) AS value',
                    'result' => [
                        'value' => 'SRID=4326;POINT(-123.365556 48.428611)',
                    ],
                ],
            ],
        ],
    ],
    'ST_SnapToGrid' => [
        'required_arguments' => 2,
        'total_arguments' => 6,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('LINESTRING(1.1115678 2.123, 4.111111 3.2374897, 4.11112 3.23748667)'),0.001)) as value1, ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING(-1.1115678 2.123 2.3456 1.11111, 4.111111 3.2374897 3.1234 1.1111, -1.11111112 2.123 2.3456 1.1111112)'), ST_GeomFromEWKT('POINT(1.12 2.22 3.2 4.4444)'), 0.1, 0.1, 0.1, 0.01)) as value2, ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING(-1.1115678 2.123 3 2.3456, 4.111111 3.2374897 3.1234 1.1111)'), 0.01)) AS value3",
                    'result' => [
                        'value1' => 'LINESTRING(1.112 2.123,4.111 3.237)',
                        'value2' => 'LINESTRING(-1.08 2.12 2.3 1.1144,4.12 3.22 3.1 1.1144,-1.08 2.12 2.3 1.1144)',
                        'value3' => 'LINESTRING(-1.11 2.12 3 2.3456,4.11 3.24 3.1234 1.1111)',
                    ],
                ],
            ],
        ],
    ],
    /*'ST_Snap' => array(
    ),*/
    'ST_Transform' => [
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => [
            'queries' => [
                [
                    'groups' => ['postgis-3.0'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))',2249),4326)) AS value",
                    'result' => [
                        'value' => 'POLYGON((-71.1776848522251 42.3902896512902,-71.1776843766326 42.3903829478009,-71.1775844305465 42.3903826677917,-71.177582592723 42.3902893647987,-71.1776848522251 42.3902896512902))',
                    ],
                ],
                [
                    'groups' => ['postgis-3.1'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))',2249),4326)) AS value",
                    'result' => [
                        'value' => 'POLYGON((-71.1776848522251 42.39028965129018,-71.17768437663261 42.390382947800894,-71.17758443054647 42.39038266779171,-71.17758259272304 42.3902893647987,-71.1776848522251 42.39028965129018))',
                    ],
                ],
                [
                    'groups' => ['postgis-3.2'],
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))',2249),4326)) AS value",
                    'result' => [
                        'value' => 'POLYGON((-71.1776848522251 42.3902896512903,-71.17768437663261 42.390382947801015,-71.17758443054647 42.390382667791854,-71.17758259272304 42.390289364798825,-71.1776848522251 42.3902896512903))',
                    ],
                ],
            ],
        ],
    ],
    'ST_Translate' => [
        'required_arguments' => 3,
        'total_arguments' => 4,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(-71.01 42.37)',4326),1,0,0)) AS value",
                    'result' => [
                        'value' => 'POINT(-70.01 42.37)',
                    ],
                ],
            ],
        ],
    ],
    'ST_TransScale' => [
        'required_arguments' => 5,
        'total_arguments' => 5,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING(1 2 3, 1 1 1)'), 0.5, 1, 1, 2)) AS value",
                    'result' => [
                        'value' => 'LINESTRING(1.5 6 3,1.5 4 1)',
                    ],
                ],
            ],
        ],
    ],
];
