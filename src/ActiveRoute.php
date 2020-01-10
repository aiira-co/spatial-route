<?php

declare(strict_types=1);

namespace Spatial\Router;


class ActiveRoute extends Route
{
    private array $_routeMaps = [];

    /**
     * @param Route ...$routeMap
     */
    public function setHttpRoutes(Route ...$routeMap): void
    {
        $this->_routeMaps = $routeMap;
    }

    public function getMaps(): array
    {
        return $this->_routeMaps;
    }

}