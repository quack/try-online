<?php

require_once './src/toolkit/QuackToolkit.php';

use \QuackCompiler\Lexer\Tokenizer;
use \QuackCompiler\Parser\SyntaxError;
use \QuackCompiler\Parser\TokenReader;

if (isset($_GET['action'])) {
  $action = trim(base64_decode($_GET['action']));

  switch ($action) {
    case ':license':
      echo file_get_contents('./LICENSE');
    default:
      $lexer = new Tokenizer($action);
      $parser = new TokenReader($lexer);

      try {
        $parser->parse();
        $parser->dumpAst();
      } catch (SyntaxError $e) {
        echo $e;
      }

      break;
  }
}
