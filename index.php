<?php

require 'vendor/autoload.php';

use Spatial\Router\Route;
use Spatial\Router\RouterModule;

$ri = new Route();

$routes = [
    $ri->mapRoute(
        'Api',
        'api/{controller}/public/{id:int}',
        new class ()
        {
            public $id = 3;
            public $content;
            function __construct()
            {
                $this->content = file_get_contents('php://input');
            }
        }
    ),
    $ri->mapRoute(
        'SuiteApi',
        'suiteapi/{controller}/public/{id}',
        new class ()
        {
            public $id = 3;
            public $content;
            function __construct()
            {
                $this->content = file_get_contents('php://input');
            }
        }
    )
];

// echo '<pre>';
// var_dump($routes);


// (new RouterModule)->routeConfig(
//     $routes,
//     [
//         'enableCache'=>true, 
//         'allowedMethod'=>'GET, POST, PUT, DELETE',
//         'CORS'=>['https://client.com', 'localhost:4200', 'localhost:3200']
//     ]
//     );

$appModule = new RouterModule();
$appModule->routeConfig(...$routes)
    ->allowedMethods('GET, POST, PUT, DELETE')
    ->enableCache(true)
    ->authGuard()
    ->defaultContentType('application/json')
    ->controllerNamespaceMap('Spatial\\{name}\\Controllers\\');
// ->defaultParams('Spatial\\{name}\\Controllers\\');

// echo (new Request)->getBody();

$appModule->render();




// $name ='Api';
// $routeTemplate = 'api/{controller}/cat/{id}';
// $defaults = new class(){
//     public $id = 3;
//     public $data;
// };
