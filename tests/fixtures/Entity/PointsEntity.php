<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

#[ORM\Entity]
#[ORM\Table(name: 'points')]
#[ORM\Index(
    fields: ['text'],
    name: 'idx_text',
)]
#[ORM\Index(
    fields: ['point'],
    name: 'idx_point',
    flags: ['spatial'],
)]
class PointsEntity
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text;

    #[ORM\Column(type: PostGISType::GEOMETRY)]
    private ?string $geometry;

    #[ORM\Column(type: PostGISType::GEOMETRY, options: ['geometry_type' => 'point'])]
    private ?string $point;

    #[ORM\Column(type: PostGISType::GEOMETRY, options: ['geometry_type' => 'point', 'srid' => 3785])]
    private ?string $point2d;

    #[ORM\Column(type: PostGISType::GEOMETRY, options: ['geometry_type' => 'pointz', 'srid' => 3785])]
    private ?string $point3dz;

    #[ORM\Column(type: PostGISType::GEOMETRY, options: ['geometry_type' => 'pointm', 'srid' => 3785])]
    private ?string $point3dm;

    #[ORM\Column(type: PostGISType::GEOMETRY, options: ['geometry_type' => 'pointzm', 'srid' => 3785])]
    private ?string $point4d;

    #[ORM\Column(type: PostGISType::GEOMETRY, nullable: true, options: ['geometry_type' => 'point', 'srid' => 3785])]
    private ?string $point2dNullable;

    #[ORM\Column(type: PostGISType::GEOMETRY, options: ['geometry_type' => 'point'])]
    private ?string $point2dNoSrid;

    #[ORM\Column(type: PostGISType::GEOGRAPHY)]
    private ?string $geography;

    #[ORM\Column(type: PostGISType::GEOGRAPHY, options: ['geometry_type' => 'point'])]
    private ?string $pointGeography2d;

    #[ORM\Column(type: PostGISType::GEOGRAPHY, options: ['geometry_type' => 'point', 'srid' => 4326])]
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

    public function getPoint2d(): ?string
    {
        return $this->point2d;
    }

    public function getPoint3dz(): ?string
    {
        return $this->point3dz;
    }

    public function getPoint3dm(): ?string
    {
        return $this->point3dm;
    }

    public function getPoint4d(): ?string
    {
        return $this->point4d;
    }

    public function getPoint2dNullable(): ?string
    {
        return $this->point2dNullable;
    }

    public function getPoint2dNoSrid(): ?string
    {
        return $this->point2dNoSrid;
    }

    public function getGeography(): ?string
    {
        return $this->geography;
    }

    public function getPointGeography2d(): ?string
    {
        return $this->pointGeography2d;
    }

    public function getPointGeography2dSrid(): ?string
    {
        return $this->pointGeography2dSrid;
    }
}
