<?php

namespace QuackCompiler\Parser;

use \Exception;
use \QuackCompiler\Lexer\Tag;
use \QuackCompiler\Lexer\Token;

define('BEGIN_ORANGE', '<span style="color: #F39C12;">');
define('END_ORANGE', '</span>');

define('BEGIN_GREEN', '<span style="color: #E74C3C;">');
define('END_GREEN', '</span>');

define('BEGIN_BG_RED', '<span style="color: #2ECC71;">');
define('END_BG_RED', '</span>');

define('BEGIN_BOLD', '<b>');
define('END_BOLD', '</b>');

class SyntaxError extends Exception
{
  private $expected;
  private $found;
  private $localization;
  private $source;

  public function expected($tag)
  {
    $this->expected = $tag;
    return $this;
  }

  public function found(Token $token)
  {
    $this->found = $token;
    return $this;
  }

  public function on(array $localization)
  {
    $this->localization = $localization;
    return $this;
  }

  public function source($source)
  {
    $this->source = $source;
    return $this;
  }

  private function extractSource()
  {
    $column = $this->localization['column'];
    $line = $this->localization['line'];

    $source_array = explode(PHP_EOL, $this->source->input);
    $source_line = $source_array[$line];

    $initial_column = $column - 10 <= 0
      ? 0
      : $this->localization['column'] - 10;

    $buffer = [];

    if ($line > 0) {
      $buffer[] = BEGIN_GREEN . "{$line} | " . $source_array[$this->localization['line'] - 1] . PHP_EOL . END_GREEN;
    }

    $buffer[] = BEGIN_GREEN . ($line + 1) . " | ";

    for ($i = $initial_column; $i < 70; $i++) {
      if (isset($source_line[$i])) {
        if ($i >= $column - $initial_column) {
          $buffer[] = BEGIN_BG_RED . $source_line[$i] . END_BG_RED;
        } else {
            $buffer[] = BEGIN_GREEN . $source_line[$i] . END_GREEN;
        }
      }
    }

    $buffer[] = PHP_EOL . str_repeat(' ', $column - $initial_column + 1);
    $buffer[] = BEGIN_BOLD . "^" . END_BOLD . str_repeat('^', 10);

    return implode($buffer);
  }

  public function __toString()
  {
    $source = $this->extractSource();

    $expected = is_integer($this->expected) ? Tag::getName($this->expected) : $this->expected;
    $found = $this->found->getTag() === 0
      ? "end of the source"
      : Tag::getName($this->found->getTag()) ?: $this->found->getTag();

    return $this->extractSource() . PHP_EOL . implode(PHP_EOL, [
      BEGIN_ORANGE,
      "*** You have a syntactic error in your code!",
      "    Expecting [{$expected}]",
      "    Found     [{$found}]",
      "    Line      {$this->localization['line']}",
      "    Column    {$this->localization['column']}",
      END_ORANGE
    ]);
  }
}
