<?php

declare(strict_types=1);

namespace Spatial\Router;

use Spatial\Router\Interface\IRouteBuilder;
use Spatial\Router\Trait\SecurityTrait;

class RouteBuilder implements IRouteBuilder
{
    use SecurityTrait;

    public string $name;
    public string $pattern;
    public object $defaults;


    public function mapRoute(string $name, string $pattern, ?object $defaults = null): self
    {
        $this->name = trim($name);
        $this->pattern = urlencode(trim($pattern, '/'));


        $this->defaults = $defaults ??
            new class {
                public string $content;

                public function __construct()
                {
                    $this->content = file_get_contents('php://input');
                }
            };




        return new $this;
    }

}
