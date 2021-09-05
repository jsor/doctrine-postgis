<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="points",
 *     indexes={
 *         @ORM\Index(name="idx_text", columns={"text"}),
 *         @ORM\Index(name="idx_point", columns={"point"}, flags={"spatial"})
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
    private ?int $id;

    /**
     * @ORM\Column(type="text", name="text")
     */
    private ?string $text;

    /**
     * @ORM\Column(type="geometry", name="geometry")
     */
    private ?string $geometry;

    /**
     * @ORM\Column(type="geometry", name="point", options={"geometry_type"="point"})
     */
    private ?string $point;

    /**
     * @ORM\Column(type="geometry", name="point_2d", options={"geometry_type"="point", "srid"=3785})
     */
    private ?string $point2D;

    /**
     * @ORM\Column(type="geometry", name="point_3dz", options={"geometry_type"="pointz", "srid"=3785})
     */
    private ?string $point3DZ;

    /**
     * @ORM\Column(type="geometry", name="point_3dm", options={"geometry_type"="pointm", "srid"=3785})
     */
    private ?string $point3DM;

    /**
     * @ORM\Column(type="geometry", name="point_4d", options={"geometry_type"="pointzm", "srid"=3785})
     */
    private ?string $point4D;

    /**
     * @ORM\Column(type="geometry", nullable=true, name="point_2d_nullable", options={"geometry_type"="point", "srid"=3785})
     */
    private ?string $point2DNullable;

    /**
     * @ORM\Column(type="geometry", name="point_2d_nosrid", options={"geometry_type"="point"})
     */
    private ?string $point2DNoSrid;

    /**
     * @ORM\Column(type="geography", name="geography")
     */
    private ?string $geography;

    /**
     * @ORM\Column(type="geography", name="point_geography_2d", options={"geometry_type"="point"})
     */
    private ?string $pointGeography2d;

    /**
     * @ORM\Column(type="geography", name="point_geography_2d_srid", options={"geometry_type"="point", "srid"=4326})
     */
    private ?string $pointGeography2dSrid;

    public function __construct(array $points)
    {
        foreach ($points as $key => $val) {
            $this->$key = $val;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGeometry(): ?string
    {
        return $this->geometry;
    }

    public function getPoint(): ?string
    {
        return $this->point;
    }

    public function getPoint2D(): ?string
    {
        return $this->point2D;
    }

    public function getPoint3DZ(): ?string
    {
        return $this->point3DZ;
    }

    public function getPoint3DM(): ?string
    {
        return $this->point3DM;
    }

    public function getPoint4D(): ?string
    {
        return $this->point4D;
    }

    public function getPoint2DNullable(): ?string
    {
        return $this->point2DNullable;
    }

    public function getPoint2DNoSrid(): ?string
    {
        return $this->point2DNoSrid;
    }

    public function getGeography(): ?string
    {
        return $this->geography;
    }

    public function getPointGeography2D(): ?string
    {
        return $this->pointGeography2d;
    }

    public function getPointGeography2DSrid(): ?string
    {
        return $this->pointGeography2dSrid;
    }
}
