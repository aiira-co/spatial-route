<?php

declare(strict_types=1);

namespace Spatial\Router;

use Spatial\Router\Trait\SecurityTrait;

class RouteTemplate
{
    use SecurityTrait;

    public string $name;
    public string $pattern;
    public object $defaults;


    public function mapRoute(string $name, string $pattern, object $defaults): self
    {
        $this->name = trim($name);
        $this->pattern = urlencode(trim($pattern, '/'));
        $this->defaults = $defaults;

        return clone $this;
    }

}
