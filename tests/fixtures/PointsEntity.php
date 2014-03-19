<?php

namespace Jsor\Doctrine\PostGIS;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="points",
 *     options={"engine"="MyISAM", "spatial_indexes"={"idx_point", "idx_not_existing"}},
 *     indexes={
 *         @ORM\Index(name="idx_text", columns={"text"}),
 *         @ORM\Index(name="idx_point", columns={"point"})
 *     }
 * )
 */
class PointsEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="text", name="text")
     */
    private $text;

    /**
     * @ORM\Column(type="geometry", name="geometry")
     */
    private $geometry;

    /**
     * @ORM\Column(type="geometry", name="point", options={"spatial_type"="point"})
     */
    private $point;

    /**
     * @ORM\Column(type="geometry", name="point_2d", options={"spatial_type"="point", "spatial_srid"=3785})
     */
    private $point2D;

    /**
     * @ORM\Column(type="geometry", name="point_3dz", options={"spatial_type"="pointz", "spatial_srid"=3785})
     */
    private $point3DZ;

    /**
     * @ORM\Column(type="geometry", name="point_3dm", options={"spatial_type"="pointm", "spatial_srid"=3785})
     */
    private $point3DM;

    /**
     * @ORM\Column(type="geometry", name="point_4d", options={"spatial_type"="pointzm", "spatial_srid"=3785})
     */
    private $point4D;

    /**
     * @ORM\Column(type="geometry", nullable=true, name="point_2d_nullable", options={"spatial_type"="point", "spatial_srid"=3785})
     */
    private $point2DNullable;

    /**
     * @ORM\Column(type="geometry", name="point_2d_nosrid", options={"spatial_type"="point"})
     */
    private $point2DNoSrid;

    /**
     * @ORM\Column(type="geography", name="geography")
     */
    private $geography;

    /**
     * @ORM\Column(type="geography", name="point_geography_2d", options={"spatial_type"="point"})
     */
    private $pointGeography2d;

    /**
     * @ORM\Column(type="geography", name="point_geography_2d_srid", options={"spatial_type"="point", "spatial_srid"=4326})
     */
    private $pointGeography2dSrid;

    public function __construct(array $points)
    {
        foreach ($points as $key => $val) {
            $this->$key = $val;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGeometry()
    {
        return $this->geometry;
    }

    public function getPoint()
    {
        return $this->point;
    }

    public function getPoint2D()
    {
        return $this->point2D;
    }

    public function getPoint3DZ()
    {
        return $this->point3DZ;
    }

    public function getPoint3DM()
    {
        return $this->point3DM;
    }

    public function getPoint4D()
    {
        return $this->point4D;
    }

    public function getPoint2DNullable()
    {
        return $this->point2DNullable;
    }

    public function getPoint2DNoSrid()
    {
        return $this->point2DNoSrid;
    }

    public function getGeography()
    {
        return $this->geography;
    }

    public function getPointGeography2D()
    {
        return $this->pointGeography2d;
    }

    public function getPointGeography2DSrid()
    {
        return $this->pointGeography2dSrid;
    }
}
