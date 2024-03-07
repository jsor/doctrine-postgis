<?php

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

final class ST_GeomFromWKB extends FunctionNode
{
    protected array $expressions = [];

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->expressions[] = $parser->ArithmeticFactor();

        $lexer = $parser->getLexer();

        if ($lexer->isNextToken(TokenType::T_COMMA)) {
            $parser->match(TokenType::T_COMMA);
            $this->expressions[] = $parser->ArithmeticFactor();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $arguments = [];

        /** @var Node $expression */
        foreach ($this->expressions as $expression) {
            $arguments[] = $expression->dispatch($sqlWalker);
        }

        return 'ST_GeomFromWKB(' . implode(', ', $arguments) . ')';
    }
}
