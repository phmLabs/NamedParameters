<?php

namespace PhmLabs\Components\NamedParameters;

function call_user_func_assoc_array($function, array $param_arr = null)
{
  $namedParameters = new NamedParameters();

  if (is_array($function))
  {
    $returnValue = $namedParameters->callMethod($function[0], $function[1], $param_arr);
  }
  else
  {
    $returnValue = $namedParameters->callFunction($function, $param_arr);
  }

  return $returnValue;
}