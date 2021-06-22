<?php

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Types\TextType;

class TsVectorType extends TextType
{
    public function getName()
    {
        return ['tsvector'];
    }
}
