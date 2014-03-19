<?php

namespace Jsor\Doctrine\PostGIS;

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
    private $id;

    /**
     * @ORM\Column(type="geometry", name="point", options={})
     */
    private $point;

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

    public function getPoint()
    {
        return $this->point;
    }
}
