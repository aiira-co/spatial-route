<?php

declare(strict_types=1);

namespace Spatial\Router\Interface;

interface IRouteBuilder
{

    public function mapRoute(string $name, string $pattern, ?object $defaults = null);
}