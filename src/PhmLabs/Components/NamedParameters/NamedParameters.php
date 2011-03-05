<?php

namespace PhmLabs\Components\NamedParameters;

class NamedParameters
{
  public function callFunction($functionName, array $parameters = array())
  {
    $reflectedFunction = new \ReflectionFunction($functionName);
    $functionParameters = $reflectedFunction->getParameters();
    $orderedParameters = $this->getOrderedParameters($functionParameters, $parameters);
    return $this->callUserFunc($functionName, $orderedParameters);
  }

  public function callMethod($object, $method, array $parameters = null)
  {
    $reflectedListener = new \ReflectionClass($object);
    $reflectedMethod = $reflectedListener->getMethod($method);
    $methodParameters = $reflectedMethod->getParameters();
    $orderedParameters = $this->getOrderedParameters($methodParameters, $parameters);
    return $this->callUserFunc(array ($object, $method ), $orderedParameters);
  }

  private function callUserFunc($function, $orderedParameters)
  {
    return call_user_func_array($function, $orderedParameters);
  }

  private function getOrderedParameters($functionParameters, array $actualParameters = array())
  {
    foreach ( $functionParameters as $parameter )
    {
      $name = $parameter->getName();
      if (array_key_exists($name, $actualParameters))
      {
        $orderedParameters[] = $actualParameters[$name];
      }
      else
      {
        if (!$parameter->isOptional())
        {
          $e = new Exception('Mandatory parameter "' . $name . '" not set.');
          $e->setMissingParameter($name);
          throw $e;
        }
        else
        {
          $orderedParameters[] = $parameter->getDefaultValue();
        }
      }
    }
    return $orderedParameters;
  }
}