<?php

namespace Mbrianp\FuncCollection\Routing;

use Mbrianp\FuncCollection\Routing\Attribute\Route;

class UrlGenerator
{
    /**
     * UrlGenerator constructor.
     * @param array<int, Route> $routes
     */
    public function __construct(protected array $routes = [])
    {
    }

    public function generateUrl(string $name, array $parameters = []): string
    {
        foreach ($this->routes as $route) {
            if ($route->name == $name) {
                $url = $route->path;

                foreach ($route->data['__parameters'] as $parameter) {
                    if (!isset($parameters[$parameter])) {
                        throw new \InvalidArgumentException(\sprintf('Parameter %s is required to generate the URL, and it was not given.', $parameter));
                    }

                    $url = str_replace('{' . $parameter . '}', $parameters[$parameter], $url);
                }

                return $url;
            }
        }

        throw new \LogicException(\sprintf('No route called %s was found', $name));
    }
}