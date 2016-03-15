<?php

namespace Test\All\Reflection;
use Test\Provider\Reflection\ClassReflector;
use Testes\Test\UnitAbstract;

class CustomDocTagTest extends UnitAbstract
{
    public function testDocTag()
    {
        $customTag = null;
        $controllerName = 'Test\Provider\Reflection\Controller';
        $controller = new ClassReflector($controllerName);

        foreach ($controller->getMethods() as $method) {
            if ($method->getDocBlock()->hasTag('custom')) {
                $customTag = $method->getDocBlock()->getTag('custom');
            }
        }

        $this->assert($customTag != null, 'Custom doc tag was not found');

        if ($customTag) {
            $this->assert($customTag->tag() == 'custom', 'Tag is incorrect');
            $this->assert($customTag->value() == 'This is a custom doc tag.', 'Value is incorrect');
        }
    }
}
