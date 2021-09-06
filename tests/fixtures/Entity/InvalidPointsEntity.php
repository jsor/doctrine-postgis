<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="invalid_points",
 *     options={"engine"="MyISAM"}
 * )
 */
class InvalidPointsEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="geometry", name="point", options={})
     */
    private ?string $point = null;

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

    public function getPoint(): ?string
    {
        return $this->point;
    }
}
