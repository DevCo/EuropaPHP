<?php

namespace Test\Provider\Reflection;

class ClassReflector extends \Europa\Reflection\ClassReflector
{
    public function getMethod($method)
    {
        return new MethodReflector($this->getName(), $method);
    }
}
