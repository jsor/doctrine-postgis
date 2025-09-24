<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Functions;

use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Lexer;

use function defined;

if (defined('Doctrine\ORM\Query\Lexer::T_IDENTIFIER')) {
    class_alias(Lexer::class, 'Jsor\Doctrine\PostGIS\Functions\ConstantWrapper');
} else {
    class_alias(TokenType::class, 'Jsor\Doctrine\PostGIS\Functions\ConstantWrapper');
}
