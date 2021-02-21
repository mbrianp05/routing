<?php

namespace Mbrianp\FuncCollection\Routing;

use Mbrianp\FuncCollection\Routing\Attribute\Route;

class Routing
{
    public function __construct(private ?Route $currentRoute, private array $routes = [])
    {
    }

    public function generateUrl(string $name, array $parameters = []): string
    {
        $urlGenerator = new UrlGenerator($this->routes);

        return $urlGenerator->generateUrl($name, $parameters);
    }

    public function getCurrentRoute(): ?Route
    {
        return $this->currentRoute;
    }
}