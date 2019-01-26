<?php
/**
 * PostgreSQL PostGIS Geometry/Geography/Box Types
 * http://postgis.net/docs/reference.html#Miscellaneous_Functions
 */
return [
    'ST_Extent' => [
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => [
            'queries' => [
                [
                    'sql' => "SELECT ST_SetSRID({function}(ST_GeometryFromText('SRID=4326;LINESTRING(-71.160281 42.258729,-71.160837 42.259113,-71.161144 42.25932)')), 4326) AS value",
                    'result' => [
                        'value' => '0103000020E61000000100000005000000957CEC2E50CA51C06EC328081E214540957CEC2E50CA51C07099D36531214540E44A3D0B42CA51C07099D36531214540E44A3D0B42CA51C06EC328081E214540957CEC2E50CA51C06EC328081E214540'
                    ]
                ],
            ]
        ]
    ],
];
