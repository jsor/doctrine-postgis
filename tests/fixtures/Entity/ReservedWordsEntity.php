<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Entity;

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
    private ?int $id;

    /**
     * @ORM\Column(type="geometry", name="`user`")
     */
    private ?string $user;

    /**
     * @ORM\Column(type="geography", name="`primary`")
     */
    private ?string $primary;

    public function __construct(array $points)
    {
        foreach ($points as $key => $val) {
            $this->$key = $val;
        }
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getPrimary(): ?string
    {
        return $this->primary;
    }
}
