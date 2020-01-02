<?php

declare(strict_types=1);

namespace Spatial\Api;

use Spatial\Router\Route;

class Router
{
    private array $apiRoutes;
    private string $api = 'Api';
    private string $apiUri = 'api';

    public function __construct(Route $route)
    {
        $this->apiRoutes = [
            $route->mapRoute(
                $this->api,
                $this->apiUri . '/{controller}/public/{id:int}',
                new class () {
                    public int $id = 3;
                    public string $content;

                    public function __construct()
                    {
                        $this->content = file_get_contents('php://input');
                    }
                }
            ),
            $route->mapRoute(
                'SuiteApi',
                'suite-api/{controller}/public/{id}',
                new class () {
                    public int $id = 3;
                    public string $content;

                    function __construct()
                    {
                        $this->content = file_get_contents('php://input');
                    }
                }
            )
        ];
    }


    public function getRoutes(): array
    {
        return $this->apiRoutes;
    }
}
