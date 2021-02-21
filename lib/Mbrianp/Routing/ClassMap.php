<?php

namespace Mbrianp\FuncCollection\Routing;

class ClassMap
{
    public static array $classes = [];

    public static function map(string $class): void
    {
          self::$classes[] = $class;
    }
}