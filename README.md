# Spatial Route - Robost request router for PHP

Set defaultContentType(), enableCache(), allowedMethods(), controllerNamespaceMap(), and authGuard() to list Authorization(CanActivate Interface) Objects

Routing is responsible for mapping request URIs to endpoint selectors and dispatching incoming requests to endpoints. Routes are defined in the app and configured when the app starts. A route can optionally extract values from the URL contained in the request, and these values can then be used for request processing. Using route information from the app, routing is also able to generate URLs that map to endpoint selectors.

## Install

You should install [Route with Composer](https://getcomposer.org):

```sh
composer require spatial/route
```

Requires PHP 7.1 or newer.

## Routing Basic

Most apps should choose a basic and descriptive routing scheme so that URLs are readable and meaningful. The default conventional route {controller=Home}/{action=Index}/{id?}:

Supports a basic and descriptive routing scheme.
Is a useful starting point for UI-based apps.
Developers commonly add additional terse routes to high-traffic areas of an app in specialized situations (for example, blog and ecommerce endpoints) using attribute routing or dedicated conventional routes.

Web APIs should use attribute routing to model the app's functionality as a set of resources where operations are represented by HTTP verbs. This means that many operations (for example, GET, POST) on the same logical resource will use the same URL. Attribute routing provides a level of control that's needed to carefully design an API's public endpoint layout.

## Usage

In Spatial Web API, a controller is a class that handles HTTP requests. The public methods of the controller are called `action` `methods` or simply `actions`. When the Web API framework receives a request, it routes the request to an action.

To determine which action to invoke, the framework uses a routing table.

```php
<?php
require '/path/to/vendor/autoload.php';

use Spatial\Router\Route;
use Spatial\Router\RouterModule;

$route = new Route();
$route->mapRoute(
    "DefaultAPI", // name
    "api/{controller}/{id}", //routeTemplate
    new class(){ public id = 2 } //defaults
);

// initialize the RouterModule to set routes
$appModule = new RouterModule();
$appModule->routeConfig($route);
// view results;
$appModule->render();
```

Each entry in the routing table contains a route template. The set route template for Spatia-Route is "api/{controller}/{id}". In this template, "api" is a literal path segment, and {controller} and {id} are placeholder variables.

When the library receives an HTTP request, it tries to match the URI against one of the route templates in the routing table. If no route matches, the client receives a 404 error. For example, the following URIs match the default route:

- /api/contacts
- /api/contacts/1
- /api/products/gizmo1
  However, the following URI does not match, because it lacks the "api" segment:

- /contacts/1

Once a matching route is found, Spatia-Route selects the controller and the action:

To find the controller, Spatia-Route adds "Controller" to the value of the {controller} variable.
To find the action, Spatia-Route looks at the HTTP verb, and then looks for an action whose name begins with that HTTP verb name. For example, with a GET request, Spatia-Route looks for an action prefixed with "Get", such as "GetContact" or "GetAllContacts". This convention applies only to GET, POST, PUT, DELETE, HEAD, OPTIONS, and PATCH verbs. (You can enable other HTTP verbs by using attributes on your controller -- future update). We'll see an example of that later.
Other placeholder variables in the route template, such as {id}, are mapped to action parameters.
Let's look at an example. Suppose that you define the following controller:

```php
<?php
public class ProductsController extends ApiController
{
    public function httpGet(int $id): ?Response { }
    public function httpDelete(int $id): Response{ }
}
```

Here are some possible HTTP requests, along with the action that gets invoked for each:
    |HTTP Verb  | URI Path	        | Method / Action   | Parameter |
    |:--------- |:-----------------:|:-----------------:| ---------:|
    |GET	    | api/products	    | httpGet	        | (none)    |
    |GET    	| api/products/4	| httpGet	        | 4         |
    |DELETE	    | api/products/4	| httpDelete	    | 4         |
    |POST	    | api/products	    | (no match)	    |           |

Notice that the {id} segment of the URI, if present, is mapped to the id parameter of the action. In this example, the controller defines GET method, one with an id parameter.

Also, note that the POST request will fail, because the controller does not define a "Post..." method.

## Routing by Action Name

With the default routing template, Web API uses the HTTP verb to select the action. However, you can also create a route where the action name is included in the URI:

```php
<?php
$route->mapRoute(
    "DefaultAPI", 
    "api/{controller}/{action}/{id}",
    new class(){ public id = 2 }
);
```
In this route template, the {action} parameter names the action method on the controller.


```php
<?php

require '/path/to/vendor/autoload.php';

use Spatial\Router\Route;
use Spatial\Router\RouterModule;

$ri = new Route();

// create an array or a single of routes
$routes = [
    $ri->mapRoute(
        // $name of the routeTemplate and name placeholder for controller namespace
        'Api',
        // $routeTemplate
        'api/{controller}/public/{id:int}',
        // default values for the routeTemplate
        new class(){
            public $id = 3;
            public $data;
        }
    ),
    $ri->mapRoute(
        'SuiteApi',
        'suiteapi/{controller}/public/{id}',
        new class(){
            public $id = 3;
            public $data;
        }
    )
];

// initialize the RouterModule to set routes
$appModule = new RouterModule();
$appModule->routeConfig($routes)
            ->allowedMethods('GET, POST, PUT, DELETE')
            ->enableCache(true)
            ->authGuard() // takes in list objects for authorization with interface CanActivate
            ->defaultContentType('application/json')
            ->controllerNamespaceMap('Spatial\\{name}\\Controllers\\'); // {name} refers to the route name


// view results;
$appModule->render();
```

### Defining route{Templates}

The routes are defined by calling the `Spatial\Router\RouterModule->routeConfig()` function, which accepts
a callable taking a `Spatial\Router\Route` instance. The routes are added by calling
`mapRoute()` on the collector instance:

```php
$r->mapRoute($name, $routeTemplate, $defaults);
```

The `$name` is a camelcase HTTP method string for which a certain route should match. It
is possible to specify multiple valid methods using an array:


### Credits

This library is based on a router in the dotNetCore WebAPI Framework
