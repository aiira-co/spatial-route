<?php

declare(strict_types=1);

namespace Spatial\Router;

use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use ReflectionMethod;
use Spatial\Interface\IRouteModule;
use Spatial\Psr7\Response;
use Spatial\Router\Trait\SecurityTrait;

class RouterModule implements  IRouteModule
{
    use SecurityTrait;
    private ActiveRouteTemplate $_routes;
    private RouteTemplate $_routeMap;
    private string $_contentType;
    private string $_namespaceMap = '';
    private bool $isRouteCached = false;

    public function routeConfig(RouteTemplate ...$routes): self
    {
        $this->_routes = new ActiveRouteTemplate;
        // var_dump($routes);
        $this->_routes->setHttpRoutes(...$routes);
        return $this;
    }

    public function enableCache(bool $isProd): self
    {
//        allow user to specify the cache-ing Driver,
//        Memcache or Redis
        $this->isRouteCached = $isProd;
        return $this;
    }


    /**
     * @param string $httpMethods
     * @return $this
     */
    public function allowedMethods(string $httpMethods): self
    {
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


    /**
     * @param string|null $uri
     * @throws ReflectionException
     */
    public function render(?string $uri = null): void
    {
//        check first fir authorization
        if (!$this->isAuthorized) {
            http_response_code(401);
            return;
        }
        $uri = $uri ?? $_SERVER['REQUEST_URI'];
        // echo $this->_resolve($uri)->getHeaderLine('Content-Type');
        $response = $this->_resolve($uri);
        $this->_setHeaders($response->getHeaders());

        // $this->_contentType = $this->_resolve($uri)->getHeaderLine('Content-Type') ?? $this->_contentType;
        // var_dump($response->getHeaders());
        http_response_code($response->getStatusCode());
        echo $response->getBody();
        // echo $this->_resolve($uri)->getBody()->getContents();
    }

    /**
     * @param string $uriString
     * @return ResponseInterface
     * @throws ReflectionException
     */
    private function _resolve(string $uriString): ResponseInterface
    {
        $uri = explode('/', trim($this->_formatRoute($uriString), '/'));
        $isValid = false;

//        if($this->cacheRoute){
////            check route with matching uri
//
//        }
        foreach ($this->_routes->getMaps() as $route) {
//            print_r($route);
            if ($route->isUriRoute($uri)) {
                $isValid = true;
//                check for authguard
                if (!$route->isAuthorized()) {
                    return (new Response())->withStatus(401, 'Unauthorized');
                }

                $this->_routeMap = $route;
                break;
            }
        }
        if (!$isValid) {
            return (new Response())->withStatus(404, 'uri doest match route template');
        }

        if (!property_exists($this->_routeMap->defaults, 'controller')) {
            return (new Response())->withStatus(404, 'Controller not specified');
        }

        $controller = $this->_getController();
        if ($controller === null) {
            return (new Response())->withStatus(404, 'Controller not found');
        }
        $method = $this->_routeMap->defaults->action ?? $this->_getRequestedMethod();
        return $this->_getControllerMethod($controller, $method);
    }

    /**
     * @param $uri
     * @return string
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

    /**
     * @return object|null
     */
    private function _getController(): ?object
    {
        // echo 'getting controller';
        $controllerNamespace = str_replace('{name}', $this->_routeMap->name, $this->_namespaceMap);
        $controller = $controllerNamespace . ucfirst($this->_routeMap->defaults->controller) . 'Controller';
        return class_exists($controller) ? new $controller : null;
    }

    /**
     * @return string
     */
    private function _getRequestedMethod(): string
    {
//        $method = 'httpGet';


        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $httpRequest = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
        } else {
            $httpRequest = $_SERVER['REQUEST_METHOD'];
        }


        return match ($httpRequest) {
            'GET' => 'httpGet',
            'POST' => 'httpPost',
            'PUT' => 'httpPut',
            'DELETE' => 'httpDelete',
            default => 'http' . ucfirst(strtolower($httpRequest)),
        };
    }

    /**
     * @param object $controller
     * @param string $method
     * @return ResponseInterface|null
     * @throws ReflectionException
     */
    private function _getControllerMethod(object $controller, string $method): ?ResponseInterface
    {
        if (method_exists($controller, $method)) {
            $r = new ReflectionMethod($controller, $method);
            $args = [];
            $params = $r->getParameters();
            foreach ($params as $param) {
                //$param is an instance of ReflectionParameter
                if (!$param->isOptional() && !property_exists($this->_routeMap->defaults, $param->getName())) {
                    die('argument ' . $param->getName() . ' required');
                }
                // echo $args;
                $args[] = $this->_routeMap->defaults->{$param->getName()};
            }

            return $controller->$method(...$args);
        }
        return null;
    }

    /**
     * @param array $headers
     */
    private function _setHeaders(array $headers): void
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
}
