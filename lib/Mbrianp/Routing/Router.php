<?php

namespace Mbrianp\FuncCollection\Routing;

use Mbrianp\FuncCollection\Http\Request;
use Mbrianp\FuncCollection\Routing\Attribute\Route;

class Router
{
    /**
     * Router constructor.
     * @param array<int, Route> $routes
     */
    public function __construct(
        public array $routes = []
    )
    {
        $this->resolveRoutesParameters();
    }

    /**
     * Converts the given Path
     * into a real Math Expression
     * to parse it.
     *
     * /contact/{name} => /contact/(?P<name>([^\/]+))
     *
     * @param string $path
     * @return string
     */
    protected function toMatchExpression(Route $route): string
    {
        $path = $route->path;

        $path = str_replace('/', '\/', $path);

        return '/^' . preg_replace('/{([a-z0-9_]+)}/i', '(?P<$1>([^\/]+))', $path) . '$/i';
    }

    protected function resolveRoutesParameters(): void
    {
        foreach ($this->routes as $route) {
            preg_match_all('/{(?P<param>[a-z0-9_]+)(<(?P<regexp>[a-z0-9_+-\/@#$^%<>~*\\\ .\[\]]+)>)?}/i', $route->path, $parameters);

            $parameters['regexp'] = \array_combine($parameters['param'], $parameters['regexp']);
            $needle = array_map(fn(string $regexp): string => '<' . $regexp . '>', $parameters['regexp']);

            // Remove the regular expression
            $route->path = str_replace($needle, '', $route->path);

            $route->data['__parameters'] = $parameters['param'];
            $route->requirements = $parameters['regexp'];
        }
    }

    protected function resolveParameters(array $unresolvedParameters): array
    {
        $resolvedParameters = [];

        foreach ($unresolvedParameters as $key => $parameter) {
            if (\is_string($key))
                $resolvedParameters[$key] = $parameter;
        }

        return $resolvedParameters;
    }

    protected function satisfiesRequirements(Route $route): bool
    {
        foreach ($route->parameters as $parameter_name => $parameter_value) {
            $requirement = $route->requirements[$parameter_name] ?? null;

            if (null === $requirement) {
                continue;
            }

            if (!preg_match('/^' . $requirement . '$/i', $parameter_value)) {
                return false;
            }
        }

        return true;
    }

    public function resolveCurrentRoute(Request $request): ?Route
    {
        $path = $request->path;

        // For adding compatibility for both kinds of URLs: /users and /users/
        if (\str_ends_with($request->path, '/')) {
            $path = \substr($request->path, 0, -1);
        }

        foreach ($this->routes as $route) {
            if (\in_array($request->method, $route->methods) && preg_match($this->toMatchExpression($route), $path, $parameters)) {
                $parameters = $this->resolveParameters($parameters);
                $route->parameters = $parameters;

                if ($this->satisfiesRequirements($route)) {
                    return $route;
                }
            }
        }

        return null;
    }

    public function hasRoutes(): bool
    {
        return !empty($this->routes);
    }
}