<?php

include_once '../src/PhmLabs/Components/bootstrap.php';

class A
{
  public function b($arg1, $arg2)
  {
    c($arg1, $arg2);
  }
}

function c($arg1, $arg2)
{
  echo $arg1 . ' ' . $arg2;
}

\PhmLabs\Components\NamedParameters\call_user_func_assoc_array('c', array( 'arg2' => 'Argument2', 'arg1' => 'Argument1'));
echo "\n";
\PhmLabs\Components\NamedParameters\call_user_func_assoc_array(array( new A, 'b'), array( 'arg2' => 'Argument2', 'arg1' => 'Argument1'));