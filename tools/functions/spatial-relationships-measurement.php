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
);
