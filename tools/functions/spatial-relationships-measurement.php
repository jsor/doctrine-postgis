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
                    'sql' => "SELECT {function}(ST_Transform(ST_GeomFromEWKT('SRID=4326;POINT(-72.1235 42.3521 4)'),2163),ST_Transform(ST_GeomFromEWKT('SRID=4326;LINESTRING(-72.1260 42.45 15, -72.123 42.1546 20)'),2163))",
                    'result' => array(
                        1 => 127.2950593251
                    )
                )
            )
        )
    ),
);
