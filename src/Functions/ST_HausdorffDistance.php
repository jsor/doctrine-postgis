<?php

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

final class ST_HausdorffDistance extends FunctionNode
{
    protected array $expressions = [];

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->expressions[] = $parser->ArithmeticFactor();

        $parser->match(Lexer::T_COMMA);

        $this->expressions[] = $parser->ArithmeticFactor();

        $lexer = $parser->getLexer();

        /** @psalm-suppress DeprecatedMethod */
        $nextType = $lexer->lookahead['type'] ?? $lexer->lookahead->type ?? null;

        if (Lexer::T_COMMA === $nextType) {
            $parser->match(Lexer::T_COMMA);
            $this->expressions[] = $parser->ArithmeticFactor();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $arguments = [];

        /** @var Node $expression */
        foreach ($this->expressions as $expression) {
            $arguments[] = $expression->dispatch($sqlWalker);
        }

        return 'ST_HausdorffDistance(' . implode(', ', $arguments) . ')';
    }
}
