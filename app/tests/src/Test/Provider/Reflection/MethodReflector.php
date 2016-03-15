<?php

namespace Test\Provider\Reflection;
use Europa\Reflection\DocBlock;

class MethodReflector extends \Europa\Reflection\MethodReflector
{
    public function getDocBlock()
    {
        $docBlock = new DocBlock();
        $docBlock->map('custom', 'Test\Provider\Reflection\DocTag\CustomTag');
        $docBlock->parse($this->getDocComment());

        return $docBlock;
    }
}
