<?php

declare(strict_types=1);

namespace Spatial\Router;

use Psr\Http\Message\ResponseInterface;
use Spatial\Psr7\Response;

class RouterModule
{

    private $_routes;
    private $_routeMap;
    private $_contentType;
    private $_namespaceMap = '';
    private $isCORS = false;

    function __construct()
    {
        $http_origin = $_SERVER['HTTP_ORIGIN'];
        header("Access-Control-Allow-Origin: $http_origin");
    }

    public function routeConfig(Route ...$routes): self
    {

        $this->_routes = new Route;
        // var_dump($routes);
        $this->_routes->setHttpRoutes(...$routes);
        return $this;
    }

    public function enableCache(bool $isprod): self
    {

        return $this;
    }

    /**
     * Set Authorizations For Route Access
     * This takes in A class with CanActivate Interface which contians a 
     * canActivate(string $url):bool method.
     * If the metod returns true, authorization is passed else denied
     *
     * @param CanActivate ...$guards
     * @return self
     */
    public function authGuard(CanActivate ...$guards): self
    {
        // cors can be part of the cors
        return $this;
    }
    /**
     * Set Allowed Methods for API
     * header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS')
     *
     * @param string $httpMethods
     * @return self
     */
    public function allowedMethods(string $httpMethods): self
    {
        header('Access-Control-Allow-Methods: ' . $httpMethods);
        return $this;
    }

    /**
     * Set CORS for API
     *header("Access-Control-Allow-Origin: *");

     * @param string ...$httpOrgins
     * @return self
     */
    public function allowedOrigins(string ...$httpOrgins): self
    {
        $this->isCORS = true;

        $http_origin = $_SERVER['HTTP_ORIGIN'];
        foreach ($httpOrgins as $orgin) {
            if ($http_origin == $orgin) {
                header("Access-Control-Allow-Origin: $http_origin");
                break;
            }
        }
        return $this;
    }

    /**
     * Sets A default Namespace Map for the controller
     * eg. Api\\{name}\\Controller\\
     * eg. Presentation\\{name}\\Controller\\
     * Placeholder 'name' will be replaced with the name arg of the matched Route
     * if 'name' is specified
     *
     * @param string $namespaceMap
     * @return self
     */
    public function controllerNamespaceMap(string $namespaceMap): self
    {
        $this->_namespaceMap = $namespaceMap;
        return $this;
    }
    public function defaultContentType(string $contentType): self
    {
        $this->_contentType = $contentType;
        return $this;
    }


    public function render(?string $uri = null)
    {
        $uri = $uri ?? $_SERVER['REQUEST_URI'];
        // echo $this->_resolve($uri)->getHeaderLine('Content-Type');
        $response = $this->_resolve($uri);
        $this->_setHeaders($response->getHeaders());

        // $this->_contentType = $this->_resolve($uri)->getHeaderLine('Content-Type') ?? $this->_contentType;
        // var_dump($response->getHeaders());

        echo $response->getBody();
        // echo $this->_resolve($uri)->getBody()->getContents();
    }



    private function _setHeaders(array $headers)
    {
        // $headerKeys = array_keys($header);
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = [$this->_contentType];
        }

        // var_dump($headers);
        foreach ($headers as $header => $values) {
            foreach ($values as $v) {
                # code...
                header($header . ':' . $v);
            }
        }
    }

    // private function defaultRequestHandler()
    // {
    //   header("{$this->request->serverProtocol} 404 Not Found");
    // }

    /**
     * Resolves a route
     */
    private function _resolve($uri)
    {

        $uri = explode('/', trim($this->_formatRoute($uri), '/'));

        $isValid = false;

        foreach ($this->_routes->getMaps() as $route) {

            if ($route->isUriRoute($uri)) {
                $isValid = true;
                $this->_routeMap = $route;
                break;
            }
        }

        if (!$isValid) {
            return (new Response())->withStatus(404, 'uri doest match route template');
        }

        if (!property_exists($this->_routeMap->defaults, 'controller')) {
            return (new Response())->withStatus(404, 'Controller not specied');
        }

        $controller = $this->_getController();
        if (\is_null($controller)) {
            return (new Response())->withStatus(404, 'Controller not found');
        }

        // if(property_exists($this->_routeMap->defaults,'action'))
        // {
        //  return $this->_getControllerMethod($controller,$this->_routeMap->defaults->action);
        // }

        $method = $this->_routeMap->defaults->action ?? $this->_getRequestedMethod();
        return $this->_getControllerMethod($controller, $method);
    }

    /**
     * Removes trailing forward slashes from the right of the route.
     * @param route (string)
     */
    private function _formatRoute($uri): string
    {
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        if ($uri === '') {
            return '/';
        }
        return $uri;
    }

    private function _getController(): ?object
    {
        $cNamspace = str_replace('{name}', $this->_routeMap->name, $this->_namespaceMap);
        $controller = $cNamspace . ucfirst($this->_routeMap->defaults->controller) . 'Controller';
        return class_exists($controller) ? new $controller : null;
    }

    private function _getControllerMethod(object $controller, string $method): ResponseInterface
    {
        if (method_exists($controller, $method)) {
            $r = new \ReflectionMethod($controller, $method);
            $args = [];
            $params = $r->getParameters();
            foreach ($params as $param) {
                //$param is an instance of ReflectionParameter
                if (!$param->isOptional() && !property_exists($this->_routeMap->defaults, $param->getName())) {

                    die('argument ' . $param->getName() . ' required');
                }

                array_push($args, $this->_routeMap->defaults->{$param->getName() ?? null});
            }

            return $controller->$method(...$args);
        }
    }




    private function _getRequestedMethod(): string
    {
        $method = 'httpGet';


        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $httpRequest = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
        } else {
            $httpRequest = $_SERVER['REQUEST_METHOD'];
        }


        switch ($httpRequest) {
            case 'GET':
                $method = 'httpGet';
                break;


            case 'POST':
                $method = 'httpPost';
                break;

            case 'PUT':
                $method = 'httpPut';
                break;

            case 'DELETE':
                $method = 'httpDelete';
                break;

            default:
                $method = 'http' . ucfirst(strtolower($httpRequest));
                break;
        }

        return $method;
    }
}
