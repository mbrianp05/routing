<?php

namespace Mbrianp\FuncCollection\Routing\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public ?string $name = null,
        public array $methods = ['GET', 'POST'],
        public array $data = [],
        public array $parameters = [],
        public array $requirements = [],
    )
    {
    }
}