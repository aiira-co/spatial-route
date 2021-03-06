<?php

declare(strict_types=1);

namespace Spatial\Router\Interfaces;

interface IRouteBuilder
{

    public function mapRoute(string $name, string $pattern, ?object $defaults = null);
}