<?php

namespace Mbrianp\FuncCollection\Routing;

use Mbrianp\FuncCollection\DIC\DIC;
use Mbrianp\FuncCollection\Kernel\ParameterResolver;
use ReflectionParameter;

class RouterParameterResolver implements ParameterResolver
{
    protected ReflectionParameter $parameter;

    public function __construct(protected DIC $dependenciesContainer)
    {
    }

    public function supports(ReflectionParameter $parameter): bool
    {
        $this->parameter = $parameter;

        return 'string' == $parameter->getType()->getName() || Routing::class == $parameter->getType()->getName();
    }

    public function resolve(): mixed
    {
        switch ($this->parameter->getType()->getName()) {
            case 'string':
                return $this->dependenciesContainer->getService('kernel.routing')->getCurrentRoute()->parameters[$this->parameter->name];
            case Routing::class:
                return $this->dependenciesContainer->getService('kernel.routing');
        }

    }
}