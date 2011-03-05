<?php

// @todo how to handle function files
include_once 'NamedParameters/functions.php';

function PhmLabs_Components__Autoload($className)
{
  $componentPrefix = 'PhmLabs\Components';

  if (strpos($className, $componentPrefix) === 0)
  {
    $relativeClassName = substr($className, strlen($componentPrefix));
    $absoluteClassPath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClassName) . '.php';
    if (file_exists($absoluteClassPath))
    {
      include_once $absoluteClassPath;
    }
  }
}

spl_autoload_register('PhmLabs_Components__Autoload');