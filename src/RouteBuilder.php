<?php

declare(strict_types=1);

namespace Spatial\Router;


use Spatial\Router\Trait\SecurityTrait;

class RouteBuilder
{
    use SecurityTrait;

    public string $name;
    public string $pattern;
    public object $defaults;

    public bool $useAttributeRouting;

    public function mapDefaultControllerRoute(): void
    {
        $this->name = 'default';
        $this->pattern = '{controller=Home}/{action=Index}/{id?}';
    }

    public function mapControllers(): void
    {
        $this->useAttributeRouting = true;
    }

    public function mapControllerRoute(string $name, string $pattern, ?object $defaults = null): self
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
