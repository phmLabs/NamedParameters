<?php

namespace PhmLabs\Components\NamedParameters;

class Exception extends \Exception
{
  private $missingParameter;

  public function setMissingParameter($paramName)
  {
    $this->missingParameter = $paramName;
  }

  public function getMissingParameter()
  {
    return $this->missingParameter;
  }
}