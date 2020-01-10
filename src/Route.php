<?php

declare(strict_types=1);

namespace Spatial\Router;

class Route
{
    use SecurityTrait;
    public string $name;
    public string $routeTemplate;
    public object $defaults;


    public function mapRoute(string $name, string $routeTemplate, object $defaults): self
    {
        $this->name = trim($name);
        $this->routeTemplate = urlencode(trim($routeTemplate, '/'));
        $this->defaults = $defaults;

        return clone $this;
    }

    /**
     * @param array $uriArr
     * @return bool
     */
    public function isUriRoute(array $uriArr): bool
    {
//        check first for authorization
        if (!$this->isAuthorized) {
            return false;
        }

        $routeArr = explode('/', trim(urldecode($this->routeTemplate), '/'));
        $routeArrCount = count($routeArr);
        // var_dump($routeArr);

        $isMatch = true;
        for ($i = 0; $i < $routeArrCount; $i++) {
            if ($routeArr[$i][0] !== '{') {
                if (!isset($uriArr[$i]) || !($routeArr[$i] === $uriArr[$i])) {
                    $isMatch = false;
                    break;
                }
            } else {
                $placeholder = str_replace(array('{', '}'), '', $routeArr[$i]);
                // check to see if its the last placeholder
                // AND if the placeholder is prefixed with `...`
                // meaning the placeholder is an array of the rest of the uriArr member
                if ($i === ($routeArrCount - 1) && strpos($placeholder, '...') === 0) {
                    $placeholder = ltrim($placeholder, '/././.');
                    if (isset($uriArr[$i])) {
                        for ($uri = $i, $uriMax = count($uriArr); $uri < $uriMax; $uri++) {
                            $this->assignValueToPlaceholder($placeholder, $uriArr[$uri], true);
                        }
                    }
                    break;
                }
                $this->assignValueToPlaceholder($placeholder, $uriArr[$i] ?? null);
            }
        }
        return $isMatch;
    }

    /**
     * @param string $placeholderString
     * @param string|null $uriValue
     * @param bool $isList
     */
    private function assignValueToPlaceholder(string $placeholderString, ?string $uriValue, bool $isList = false): void
    {
        // separate constraints
        $placeholder = explode(':', $placeholderString);

        $value = $uriValue ?? $this->defaults->{$placeholder[0]} ?? null;

        if (isset($placeholder[1])) {
            $typeValue = explode('=', $placeholder[1]);
            if (isset($typeValue[1])) {
                $value = $value ?? $typeValue[1];
            }
            if ($value !== null) {
                switch ($placeholder[1]) {
                    case 'int':
                        $value = (int)$value;
                        break;

                    case 'bool':
                        $value = (bool)$value;
                        break;

                    case 'string':
                        $value = (string)$value;
                        break;

                    case 'array':
                        $value = (array)$value;
                        break;
                    case 'float':
                        $value = (float)$value;
                        break;

                    case 'object':
                        $value = (object)$value;
                        break;

                    default:
                        # code...
                        $value = (string)$value;
                        break;
                }
            }
        }
        // set value
        $isList ? $this->defaults->{$placeholder[0]}[] = $value : $this->defaults->{$placeholder[0]} = $value;
    }
}
