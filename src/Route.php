<?php

declare(strict_types=1);

namespace Spatial\Router;

class Route
{

  private $_routeMaps = [];


  public $name;
  public $routeTemplate;
  public $defaults;

  public function mapHttpRoute(string $name, string $routeTemplate, object $defaults): self
  {
    $this->name = trim($name);
    $this->routeTemplate = urlencode(trim($routeTemplate, '/'));
    $this->defaults = $defaults;

    return clone $this;
  }

  public function setHttpRoutes(Route ...$routeMap)
  {
    $this->_routeMaps = $routeMap;
  }

  public function getMaps(): array
  {
    return $this->_routeMaps;
  }

  public function isUriRoute(array $uriArr): bool
  {
    $routeArr = explode('/', trim(urldecode($this->routeTemplate), '/'));
    // var_dump($routeArr);

    $isMatch = true;
    for ($i = 0; $i < count($routeArr); $i++) {
      if ($routeArr[$i][0] !== '{') {
        if (!($routeArr[$i] === $uriArr[$i])) {
          $isMatch = false;
          break;
        }
      } else {
        // echo $routeArr[$i];

        $placeholder = str_replace('}', '', str_replace('{', '', $routeArr[$i]));
        $value = $uriArr[$i] ?? $this->defaults->{$placeholder[0]} ?? null;
        // echo $placeholder;
        // separate contraints
        $placeholder = explode(':', $placeholder);
        if (isset($placeholder[1])) {
          $typeValue = explode('=', $placeholder[1]);
          if (isset($typeValue[1])) {
            $value = $value ?? $typeValue[1];
          }

          switch ($placeholder[1]) {
            case 'int':
              $value = (int) $value;
              break;

            case 'bool':
              $value = (bool) $value;
              break;

            case 'string':
              $value = (string) $value;
              break;

            case 'array':
              $value = (array) $value;
              break;
            case 'float':
              $value = (array) $value;
              break;

            case 'object':
              $value = (object) $value;
              break;

            default:
              # code...
              break;
          }
        }
        $this->defaults->{$placeholder[0]} = $value;
      }
    }
    return $isMatch;
  }
}
