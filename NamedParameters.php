<?php

namespace PhmLabs\Components\NamedParameters;

/**
 * This class is used to call a function/method with named parameters. This means
 * the parameters are given as an associative array and will be ordered to fit the
 * function signature.
 *
 * @todo add unit tests
 *
 * @author Nils Langner <langner@phmlabs.com>
 * @link http://www.phmlabs.com/Components/NamedParameters
 */
class NamedParameters
{
    /**
     * This function calls a function with the given parameters
     *
     * @param string $functionName
     * @param array $parameters
     *
     * @return mixed
     */
    public function callFunction($functionName, array $parameters = array())
    {
        $reflectedFunction = new \ReflectionFunction($functionName);
        $functionParameters = $reflectedFunction->getParameters();
        $orderedParameters = $this->getOrderedParameters($functionParameters, $parameters);
        return $this->callUserFunc($functionName, $orderedParameters);
    }

    /**
     * This function calls a method with the given parameters
     *
     * @param Object $object
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function callMethod($object, $method, array $parameters = array())
    {
        $reflectedListener = new \ReflectionClass($object);
        try {
            $reflectedMethod = $reflectedListener->getMethod($method);
        } catch (\ReflectionException $e) {
            throw new Exception('call method (' . get_class($object) . '::' . $method . ' failed. ' . $e->getMessage());
        }
        $methodParameters = $reflectedMethod->getParameters();

        try {
            $orderedParameters = $this->getOrderedParameters($methodParameters, $parameters);
        } catch (Exception $e) {
            throw new Exception('Unable calling ' . get_class($object) . ':' . $method . ' with message: ' . $e->getMessage());
        }
        return $this->callUserFunc(array($object, $method), $orderedParameters);
    }

    /**
     * This function calls the natice call_user_func_array function.
     *
     * @param callback $function
     * @param parameters $orderedParameters
     */
    private function callUserFunc($function, $orderedParameters)
    {
        return call_user_func_array($function, $orderedParameters);
    }

    /**
     * This function returns the list of the ordared parameters
     *
     * @param array $functionParameters The paramaters the function expects $functionParameters
     * @param array $actualParameters The given parameters
     * @return array
     * @throws \Exception
     */
    private function getOrderedParameters($functionParameters, array $actualParameters = array())
    {
        $orderedParameters = array();

        foreach ($functionParameters as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $actualParameters)) {
                $orderedParameters[] = $actualParameters[$name];
            } else {
                if (!$parameter->isOptional()) {
                    $e = new Exception('Mandatory parameter "' . $name . '" not set.');
                    $e->setMissingParameter($name);
                    throw $e;
                } else {
                    $orderedParameters[] = $parameter->getDefaultValue();
                }
            }
        }
        return $orderedParameters;
    }

    public static function call($function, array $param_arr = null)
    {
        $namedParameters = new NamedParameters();
        if (is_array($function)) {
            $returnValue = $namedParameters->callMethod($function[0], $function[1], $param_arr);
        } else {
            $returnValue = $namedParameters->callFunction($function, $param_arr);
        }
        return $returnValue;
    }

    /**
     * @param $className
     * @param array|null $param_arr
     * @return mixed
     * @throws Exception
     * @throws \ReflectionException
     */
    public static function construct($className, array $param_arr = null)
    {
        $namedParameters = new self();

        $reflectedListener = new \ReflectionClass($className);
        $reflectedMethod = $reflectedListener->getMethod('__construct');
        $methodParameters = $reflectedMethod->getParameters();

        try {
            $orderedParameters = $namedParameters->getOrderedParameters($methodParameters, $param_arr);
            $object = new $className(...$orderedParameters);
        } catch (\Exception $e) {
            throw new Exception('Unable calling ' . $className . ':__construct with message: ' . $e->getMessage());
        }

        return $object;
    }

    public static function normalizeParameters(array $params)
    {
        $parameters = array();
        foreach ($params as $parameter) {
            if (!is_array($parameter)) {
                throw new \RuntimeException("The given parameters can not be converted.");
            }
            foreach ($parameter as $key => $value) {
                $parameters[$key] = $value;
            }
        }
        return $parameters;
    }
}
