<?php
/**
 * Quack Compiler and toolkit
 * Copyright (C) 2016 Marcelo Camargo <marcelocamargo@linuxmail.org> and
 * CONTRIBUTORS.
 *
 * This file is part of Quack.
 *
 * Quack is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Quack is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Quack.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace QuackCompiler\Ast\Stmt;

use \QuackCompiler\Ast\Stmt\BlockStmt;
use \QuackCompiler\Parser\Parser;

class ForStmt implements Stmt
{
    public $variable;
    public $from;
    public $to;
    public $by;
    public $body;

    public function __construct($variable, $from, $to, $by, $body)
    {
        $this->variable = $variable;
        $this->from = $from;
        $this->to = $to;
        $this->by = $by;
        $this->body = $body;
    }

    public function format(Parser $parser)
    {
        $source = 'for ';
        $source .= $this->variable;
        $source .= ' from ';
        $source .= $this->from->format($parser);
        $source .= ' to ';
        $source .= $this->to->format($parser);

        if (null !== $this->by) {
            $source .= ' by ';
            $source .= $this->by->format($parser);
        }

        $source .= PHP_EOL;

        $parser->openScope();

        foreach ($this->body as $stmt) {
            $source .= $parser->indent();
            $source .= $stmt->format($parser);
        }

        $parser->closeScope();

        $source .= $parser->indent();
        $source .= 'end';
        $source .= PHP_EOL;
        return $source;
    }
}
