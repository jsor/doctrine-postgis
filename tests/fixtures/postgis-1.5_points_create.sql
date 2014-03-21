CREATE SEQUENCE points_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE points (id INT NOT NULL, text TEXT NOT NULL, tsvector TSVECTOR NOT NULL, geography geography(GEOMETRY, 4326) NOT NULL, point_geography_2d geography(POINT) NOT NULL, point_geography_2d_srid geography(POINT, 4326) NOT NULL, PRIMARY KEY(id));

SELECT AddGeometryColumn('points', 'geometry', -1, 'GEOMETRY', 2);
ALTER TABLE points ALTER geometry SET NOT NULL;

SELECT AddGeometryColumn('points', 'point', -1, 'POINT', 2);
ALTER TABLE points ALTER point SET NOT NULL;

SELECT AddGeometryColumn('points', 'point_2d', 3785, 'POINT', 2);
ALTER TABLE points ALTER point_2d SET NOT NULL;

SELECT AddGeometryColumn('points', 'point_3dz', 3785, 'POINT', 3);
ALTER TABLE points ALTER point_3dz SET NOT NULL;

SELECT AddGeometryColumn('points', 'point_3dm', 3785, 'POINTM', 3);
ALTER TABLE points ALTER point_3dm SET NOT NULL;

SELECT AddGeometryColumn('points', 'point_4d', 3785, 'POINT', 4);
ALTER TABLE points ALTER point_4d SET NOT NULL;

SELECT AddGeometryColumn('points', 'point_2d_nullable', 3785, 'POINT', 2);

SELECT AddGeometryColumn('points', 'point_2d_nosrid', -1, 'POINT', 2);
ALTER TABLE points ALTER point_2d_nosrid SET NOT NULL;

CREATE INDEX idx_text ON points (text);
CREATE INDEX idx_text_gist ON points USING gist(tsvector);
CREATE INDEX IDX_27BA8E29B7A5F324 ON points USING GIST (point);
CREATE INDEX IDX_27BA8E2999674A3D ON points USING GIST (point_2d);
CREATE INDEX IDX_27BA8E293BE136C3 ON points USING GIST (point_3dz);
CREATE INDEX IDX_27BA8E29B832B304 ON points USING GIST (point_3dm);
CREATE INDEX IDX_27BA8E29CF3DEDBB ON points USING GIST (point_4d);
CREATE INDEX IDX_27BA8E293C257075 ON points USING GIST (point_2d_nullable);
CREATE INDEX IDX_27BA8E293D5FE69E ON points USING GIST (point_2d_nosrid);
CREATE INDEX IDX_27BA8E295F51A43C ON points USING GIST (point_geography_2d);
CREATE INDEX IDX_27BA8E295AFBB72D ON points USING GIST (point_geography_2d_srid);
