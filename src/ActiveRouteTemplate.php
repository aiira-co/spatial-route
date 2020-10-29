<?php

declare(strict_types=1);

namespace Spatial\Router;


class ActiveRouteTemplate extends RouteTemplate
{
    private array $_routeMaps = [];

    /**
     * @param RouteTemplate ...$routeMap
     */
    public function setHttpRoutes(RouteTemplate ...$routeMap): void
    {
        $this->_routeMaps = $routeMap;
    }

    public function getMaps(): array
    {
        return $this->_routeMaps;
    }

}