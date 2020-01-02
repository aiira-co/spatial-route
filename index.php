<?php

require 'vendor/autoload.php';

use Spatial\Api\Router;
use Spatial\Router\Route;
use Spatial\Router\RouterModule;

$ri = new Route();

$routes = [
    ... (new Router($ri))->getRoutes()
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

try {
    $appModule->render();
} catch (ReflectionException $e) {
//    http_response_code($e->getCode());
    echo $e->getMessage();
}




// $name ='Api';
// $routeTemplate = 'api/{controller}/cat/{id}';
// $defaults = new class(){
//     public $id = 3;
//     public $data;
// };
