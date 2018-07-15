<?php
/**
 * Geometry Constructors
 * http://postgis.net/docs/reference.html#Geometry_Constructors
 */
return array(
    'ST_Box2dFromGeoHash' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'group' => array('postgis-2.x', 'postgis-2.1'),
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('9qqj7nmxncgyy4d0dbxqz0') AS value",
                    'result' => array(
                        'value' => 'BOX(-115.172816 36.114646,-115.172816 36.114646)'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('9qqj7nmxncgyy4d0dbxqz0', 0) AS value",
                    'result' => array(
                        'value' => 'BOX(-180 -90,180 90)'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('9qqj7nmxncgyy4d0dbxqz0', 10) AS value",
                    'result' => array(
                        'value' => 'BOX(-115.17282128334 36.1146408319473,-115.172810554504 36.1146461963654)'
                    )
                ),
            )
        )
    ),
    'ST_GeogFromText' => array(
        'alias_for' => 'ST_GeographyFromText',
    ),
    'ST_GeographyFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('SRID=4326;LINESTRING(-71.160281 42.258729,-71.160837 42.259113,-71.161144 42.25932)') AS value",
                    'result' => array(
                        'value' => '0102000020E610000003000000E44A3D0B42CA51C06EC328081E21454027BF45274BCA51C0F67B629D2A214540957CEC2E50CA51C07099D36531214540'
                    )
                ),
            )
        )
    ),
    'ST_GeogFromWKB' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'group' => 'postgis-2.x',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}(ST_GeogFromText('LINESTRING(-113.98 39.198,-113.981 39.195)'))) AS value",
                    'result' => array(
                        'value' => 'LINESTRING(-113.98 39.198,-113.981 39.195)'
                    )
                ),
            )
        )
    ),
    'ST_GeomCollFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('GEOMETRYCOLLECTION(POINT(1 2),LINESTRING(1 2, 3 4))') AS value",
                    'result' => array(
                        'value' => '0107000000020000000101000000000000000000F03F0000000000000040010200000002000000000000000000F03F000000000000004000000000000008400000000000001040'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('GEOMETRYCOLLECTION(POINT(1 2),LINESTRING(1 2, 3 4))', 4326) AS value",
                    'result' => array(
                        'value' => '0107000020E6100000020000000101000000000000000000F03F0000000000000040010200000002000000000000000000F03F000000000000004000000000000008400000000000001040'
                    )
                ),
            )
        )
    ),
    'ST_GeomFromEWKB' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_AsEWKB(ST_GeomFromText('POLYGON((0 0,0 1,1 1,1 0,0 0))',4326))) AS value",
                    'result' => array(
                        'value' => '0103000020E61000000100000005000000000000000000000000000000000000000000000000000000000000000000F03F000000000000F03F000000000000F03F000000000000F03F000000000000000000000000000000000000000000000000'
                    )
                ),
            )
        )
    ),
    'ST_GeomFromEWKT' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('SRID=4269;LINESTRING(-71.160281 42.258729,-71.160837 42.259113,-71.161144 42.25932)') AS value",
                    'result' => array(
                        'value' => '0102000020AD10000003000000E44A3D0B42CA51C06EC328081E21454027BF45274BCA51C0F67B629D2A214540957CEC2E50CA51C07099D36531214540'
                    )
                ),
            )
        )
    ),
    'ST_GeomFromGeoHash' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'group' => array('postgis-2.x', 'postgis-2.1'),
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}('9qqj7nmxncgyy4d0dbxqz0')) AS value",
                    'result' => array(
                        'value' => 'POLYGON((-115.172816 36.114646,-115.172816 36.114646,-115.172816 36.114646,-115.172816 36.114646,-115.172816 36.114646))'
                    )
                ),
                array(
                    'sql' => "SELECT ST_AsText({function}('9qqj7nmxncgyy4d0dbxqz0', 4)) AS value",
                    'result' => array(
                        'value' => 'POLYGON((-115.3125 36.03515625,-115.3125 36.2109375,-114.9609375 36.2109375,-114.9609375 36.03515625,-115.3125 36.03515625))'
                    )
                ),
            )
        )
    ),
    'ST_GeomFromGML' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                // @TODO: Get this to work with PostGIS 1.5 (SQLSTATE[XX000]: Internal error: 7 ERROR:  invalid GML representation)
                // See https://travis-ci.org/jsor/doctrine-postgis/jobs/55927677
                /*array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_AsGML(ST_GeomFromText('POLYGON((0 0,0 1,1 1,1 0,0 0))')))) AS value",
                    'result' => array(
                        'value' => 'POLYGON((0 0,0 1,1 1,1 0,0 0))'
                    )
                ),*/
                array(
                    'group' => 'postgis-2.x',
                    'sql' => "SELECT ST_AsEWKT({function}('<gml:Polygon srsName=\"EPSG:4326\"><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>0,0 0,1 1,1 1,0 0,0</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon>')) AS value",
                    'result' => array(
                        'value' => 'SRID=4326;POLYGON((0 0,0 1,1 1,1 0,0 0))'
                    )
                ),
                array(
                    'group' => 'postgis-2.x',
                    'sql' => "SELECT ST_AsEWKT({function}('<gml:LineString><gml:coordinates>-71.16028,42.258729 -71.160837,42.259112 -71.161143,42.25932</gml:coordinates></gml:LineString>', 4326)) AS value",
                    'result' => array(
                        'value' => 'SRID=4326;LINESTRING(-71.16028 42.258729,-71.160837 42.259112,-71.161143 42.25932)'
                    )
                ),
            )
        )
    ),
    'ST_GeomFromGeoJSON' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'group' => 'postgis-2.x',
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}('{\"type\":\"Point\",\"coordinates\":[-48.23456,20.12345]}')) AS value",
                    'result' => array(
                        'value' => 'POINT(-48.23456 20.12345)'
                    )
                ),
                array(
                    'sql' => "SELECT ST_AsText({function}('{\"type\":\"LineString\",\"coordinates\":[[1,2,3],[4,5,6],[7,8,9]]}')) AS value",
                    'result' => array(
                        'value' => 'LINESTRING Z (1 2 3,4 5 6,7 8 9)'
                    )
                ),
            )
        )
    ),
    'ST_GeomFromKML' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}('<LineString><coordinates>-71.1663,42.2614 -71.1667,42.2616</coordinates></LineString>')) AS value",
                    'result' => array(
                        'value' => 'LINESTRING(-71.1663 42.2614,-71.1667 42.2616)'
                    )
                ),
            )
        )
    ),
    'ST_GeomFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('LINESTRING(-71.160281 42.258729,-71.160837 42.259113,-71.161144 42.25932)') AS value",
                    'result' => array(
                        'value' => '010200000003000000E44A3D0B42CA51C06EC328081E21454027BF45274BCA51C0F67B629D2A214540957CEC2E50CA51C07099D36531214540'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('LINESTRING(-71.160281 42.258729,-71.160837 42.259113,-71.161144 42.25932)',4269) AS value",
                    'result' => array(
                        'value' => '0102000020AD10000003000000E44A3D0B42CA51C06EC328081E21454027BF45274BCA51C0F67B629D2A214540957CEC2E50CA51C07099D36531214540'
                    )
                ),
            )
        )
    ),
    'ST_GeometryFromText' => array(
        'alias_for' => 'ST_GeomFromText',
    ),
    'ST_GeomFromWKB' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_AsBinary(ST_GeomFromText('POLYGON((0 0,0 1,1 1,1 0,0 0))')),4326) AS value",
                    'result' => array(
                        'value' => '0103000020E61000000100000005000000000000000000000000000000000000000000000000000000000000000000F03F000000000000F03F000000000000F03F000000000000F03F000000000000000000000000000000000000000000000000'
                    )
                ),
            )
        )
    ),
    'ST_LineFromMultiPoint' => array(
        'required_arguments' => 1,
        'total_arguments' => 1,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(ST_GeomFromEWKT('MULTIPOINT(1 2 3, 4 5 6, 7 8 9)'))) AS value",
                    'result' => array(
                        'value' => 'LINESTRING(1 2 3,4 5 6,7 8 9)'
                    )
                ),
            )
        )
    ),
    'ST_LineFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('LINESTRING(1 2, 3 4)', 4326) AS value",
                    'result' => array(
                        'value' => '0102000020E610000002000000000000000000F03F000000000000004000000000000008400000000000001040'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('POINT(1 2)') AS value",
                    'result' => array(
                        'value' => null
                    )
                ),
            )
        )
    ),
    'ST_LineFromWKB' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_AsBinary(ST_GeomFromText('LINESTRING(1 2, 3 4)')), 4326) AS value",
                    'result' => array(
                        'value' => '0102000020E610000002000000000000000000F03F000000000000004000000000000008400000000000001040'
                    )
                ),
                array(
                    'sql' => "SELECT {function}(ST_AsBinary(ST_GeomFromText('POINT(1 2)'))) AS value",
                    'result' => array(
                        'value' => null
                    )
                ),
            )
        )
    ),
    'ST_LinestringFromWKB' => array(
        'alias_for' => 'ST_LineFromWKB'
    ),
    'ST_MakeBox2D' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_MakePoint(-989502.1875, 528439.5625), ST_MakePoint(-987121.375 ,529933.1875)) AS value",
                    'result' => array(
                        'value' => 'BOX(-989502.1875 528439.5625,-987121.375 529933.1875)'
                    )
                ),
            )
        )
    ),
    'ST_3DMakeBox' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'group' => 'postgis-2.x',
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_MakePoint(-989502.1875, 528439.5625, 10), ST_MakePoint(-987121.375 ,529933.1875, 10)) AS value",
                    'result' => array(
                        'value' => 'BOX3D(-989502.1875 528439.5625 10,-987121.375 529933.1875 10)'
                    )
                ),
            )
        )
    ),
    'ST_MakeBox3D' => array(
        'alias_for' => 'ST_3DMakeBox',
        'tests' => array(
            'group' => 'postgis-1.5',
        )
    ),
    'ST_MakeLine' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText(ST_MakeLine(ST_MakePoint(1,2), ST_MakePoint(3,4))) AS value",
                    'result' => array(
                        'value' => 'LINESTRING(1 2,3 4)'
                    )
                ),
            )
        )
    ),
    'ST_MakeEnvelope' => array(
        'required_arguments' => 4,
        'total_arguments' => 5,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT(ST_MakeEnvelope(10, 10, 11, 11, 4326)) AS value",
                    'result' => array(
                        'value' => 'SRID=4326;POLYGON((10 10,10 11,11 11,11 10,10 10))'
                    )
                ),
            )
        )
    ),
    'ST_MakePolygon' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT(ST_MakePolygon(ST_GeomFromText('LINESTRING(75.15 29.53,77 29,77.6 29.5, 75.15 29.53)'))) AS value",
                    'result' => array(
                        'value' => 'POLYGON((75.15 29.53,77 29,77.6 29.5,75.15 29.53))'
                    )
                ),
            )
        )
    ),
    'ST_MakePoint' => array(
        'required_arguments' => 2,
        'total_arguments' => 4,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(1, 2, 1.5, 2) AS value",
                    'result' => array(
                        'value' => '01010000C0000000000000F03F0000000000000040000000000000F83F0000000000000040'
                    )
                ),
            )
        )
    ),
    'ST_MakePointM' => array(
        'required_arguments' => 3,
        'total_arguments' => 3,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsEWKT({function}(-71.1043443253471, 42.3150676015829, 10)) AS value",
                    'result' => array(
                        'value' => 'POINTM(-71.1043443253471 42.3150676015829 10)'
                    )
                ),
            )
        )
    ),
    'ST_MLineFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('MULTILINESTRING((1 2, 3 4), (4 5, 6 7))', 4326) AS value",
                    'result' => array(
                        'value' => '0105000020E610000002000000010200000002000000000000000000F03F0000000000000040000000000000084000000000000010400102000000020000000000000000001040000000000000144000000000000018400000000000001C40'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('POINT(1 2)') AS value",
                    'result' => array(
                        'value' => null
                    )
                ),
            )
        )
    ),
    'ST_MPointFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('MULTIPOINT(-70.9590 42.1180, -70.9611 42.1223)', 4326) AS value",
                    'result' => array(
                        'value' => '0104000020E61000000200000001010000004C37894160BD51C0C976BE9F1A0F45400101000000E10B93A982BD51C08126C286A70F4540'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('POINT(1 2)') AS value",
                    'result' => array(
                        'value' => null
                    )
                ),
            )
        )
    ),
    'ST_MPolyFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('MULTIPOLYGON(((0 0 1,20 0 1,20 20 1,0 20 1,0 0 1),(5 5 3,5 7 3,7 7 3,7 5 3,5 5 3)))', 4326) AS value",
                    'result' => array(
                        'value' => '01060000A0E6100000010000000103000080020000000500000000000000000000000000000000000000000000000000F03F00000000000034400000000000000000000000000000F03F00000000000034400000000000003440000000000000F03F00000000000000000000000000003440000000000000F03F00000000000000000000000000000000000000000000F03F0500000000000000000014400000000000001440000000000000084000000000000014400000000000001C4000000000000008400000000000001C400000000000001C4000000000000008400000000000001C4000000000000014400000000000000840000000000000144000000000000014400000000000000840'
                    )
                ),
                array(
                    'sql' => "SELECT {function}('POINT(1 2)') AS value",
                    'result' => array(
                        'value' => null
                    )
                ),
            )
        )
    ),
    'ST_Point' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(1, 2) AS value",
                    'result' => array(
                        'value' => '0101000000000000000000F03F0000000000000040'
                    )
                ),
            )
        )
    ),
    'ST_PointFromGeoHash' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'group' => array('postgis-2.x', 'postgis-2.1'),
            'queries' => array(
                array(
                    'sql' => "SELECT ST_AsText({function}('9qqj7nmxncgyy4d0dbxqz0')) AS value",
                    'result' => array(
                        'value' => 'POINT(-115.172816 36.114646)'
                    )
                ),
                array(
                    'sql' => "SELECT ST_AsText({function}('9qqj7nmxncgyy4d0dbxqz0', 4)) AS value",
                    'result' => array(
                        'value' => 'POINT(-115.13671875 36.123046875)'
                    )
                ),
            )
        )
    ),
    'ST_PointFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('POINT(-71.064544 42.28787)', 4326) AS value",
                    'result' => array(
                        'value' => '0101000020E6100000CB49287D21C451C0F0BF95ECD8244540'
                    )
                ),
            )
        )
    ),
    'ST_PointFromWKB' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_AsEWKB(ST_PointFromText('POINT(-71.064544 42.28787)')), 4326) AS value",
                    'result' => array(
                        'value' => '0101000020E6100000CB49287D21C451C0F0BF95ECD8244540'
                    )
                ),
            )
        )
    ),
    'ST_Polygon' => array(
        'required_arguments' => 2,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}(ST_GeomFromText('LINESTRING(75.15 29.53,77 29,77.6 29.5, 75.15 29.53)'), 4326) AS value",
                    'result' => array(
                        'value' => '0103000020E610000001000000040000009A99999999C9524048E17A14AE873D4000000000004053400000000000003D4066666666666653400000000000803D409A99999999C9524048E17A14AE873D40'
                    )
                ),
            )
        )
    ),
    'ST_PolygonFromText' => array(
        'required_arguments' => 1,
        'total_arguments' => 2,
        'tests' => array(
            'queries' => array(
                array(
                    'sql' => "SELECT {function}('POLYGON((-71.1776585052917 42.3902909739571,-71.1776820268866 42.3903701743239,-71.1776063012595 42.3903825660754,-71.1775826583081 42.3903033653531,-71.1776585052917 42.3902909739571))', 4326) AS value",
                    'result' => array(
                        'value' => '0103000020E610000001000000050000006285C7C15ECB51C0ED88FC0DF531454028A46F245FCB51C009075EA6F731454047DED1E65DCB51C0781C510EF83145404871A7835DCB51C0EBDAEE75F53145406285C7C15ECB51C0ED88FC0DF5314540'
                    )
                ),
            )
        )
    ),
);
