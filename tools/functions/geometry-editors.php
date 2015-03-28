<?php
/**
 * Geometry Editors
 * http://postgis.net/docs/reference.html#Geometry_Editors
 */
return array(
    /*
    'ST_AddPoint' => array(
    ),
    'ST_Affine' => array(
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
    ),
    */
    'ST_Multi' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))')))",
                    'result' => array(
                        1 => 'MULTIPOLYGON(((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416)))'
                    )
                ),
            )
        )
    ),
    /*
    'ST_RemovePoint' => array(
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
    ),
    */
    'ST_Scale' => array(
        'required_arguments' => 3,
        'total_arguments' => 4,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING(1 2 3, 1 1 1)'), 0.5, 0.75, 0.8))",
                    'result' => array(
                        1 => 'LINESTRING(0.5 1.5 2.4,0.5 0.75 0.8)'
                    )
                ),
            )
        )
    ),
    /*
    'ST_Segmentize' => array(
    ),
    'ST_SetPoint' => array(
    ),
    */
    'ST_SetSRID' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_Point(-123.365556, 48.428611),4326))",
                    'result' => array(
                        1 => 'SRID=4326;POINT(-123.365556 48.428611)'
                    )
                ),
            )
        )
    ),
    /*
    'ST_SnapToGrid' => array(
    ),
    'ST_Snap' => array(
    ),
    */
    'ST_Transform' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))',2249),4326))",
                    'result' => array(
                        1 => 'POLYGON((-71.1776848522251 42.3902896512902,-71.1776843766326 42.3903829478009,-71.1775844305465 42.3903826677917,-71.1775825927231 42.3902893647987,-71.1776848522251 42.3902896512902))'
                    )
                ),
            )
        )
    ),
    'ST_Translate' => array(
        'required_arguments' => 3,
        'total_arguments' => 4,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeomFromText('POINT(-71.01 42.37)',4326),1,0))",
                    'result' => array(
                        1 => 'POINT(-70.01 42.37)'
                    )
                ),
            )
        )
    ),
    'ST_TransScale' => array(
        'required_arguments' => 5,
        'total_arguments' => 5,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('LINESTRING(1 2 3, 1 1 1)'), 0.5, 1, 1, 2))",
                    'result' => array(
                        1 => 'LINESTRING(1.5 6 3,1.5 4 1)'
                    )
                ),
            )
        )
    ),
);
