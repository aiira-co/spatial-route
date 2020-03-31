<?php

namespace Spatial\Router;

trait SecurityTrait
{
    private bool $isAuthorized = true;

    /**
     * @param CanActivate ...$guards
     * @return $this
     */
    public function authGuard(CanActivate ...$guards): self
    {
        // cors can be part of the cors
        foreach ($guards as $guard) {
            if (!$guard->canActivate($_SERVER['REQUEST_URI'])) {
                $this->isAuthorized = false;
                break;
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->isAuthorized;
    }
}