<?php

namespace Jsor\Doctrine\PostGIS;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity with reserved words as table and column names.
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="`user`"
 * )
 */
class ReservedWordsEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="geometry", name="`user`")
     */
    private $user;

    /**
     * @ORM\Column(type="geography", name="`primary`")
     */
    private $primary;

    public function __construct(array $points)
    {
        foreach ($points as $key => $val) {
            $this->$key = $val;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPrimary()
    {
        return $this->primary;
    }
}
