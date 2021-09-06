CREATE SEQUENCE points_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE points (id INT NOT NULL, text TEXT NOT NULL, tsvector TSVECTOR NOT NULL, geometry geometry(GEOMETRY, 0) NOT NULL, point geometry(POINT) NOT NULL, point_2d geometry(POINT, 3785) NOT NULL, point_3dz geometry(POINTZ, 3785) NOT NULL, point_3dm geometry(POINTM, 3785) NOT NULL, point_4d geometry(POINTZM, 3785) NOT NULL, point_2d_nullable geometry(POINT, 3785) DEFAULT NULL, point_2d_nosrid geometry(POINT) NOT NULL, geography geography(GEOMETRY, 4326) NOT NULL, point_geography_2d geography(POINT) NOT NULL, point_geography_2d_srid geography(POINT, 4326) NOT NULL, PRIMARY KEY(id));

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
