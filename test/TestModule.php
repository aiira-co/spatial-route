<?php

declare(strict_types=1);


namespace Spatial\Api;

use Spatial\Api\Controllers\ProductController;
use Spatial\Api\Controllers\TestController;
use Spatial\Api\Controllers\ValuesController;
use Spatial\Common\CommonModule;
use Spatial\Core\Attributes\ApiModule;
use Spatial\Core\Interfaces\IApplicationBuilder;
use Spatial\Core\Interfaces\IWebHostEnvironment;
use Spatial\Router\RouteBuilder;


#[ApiModule(
    imports: [
    CommonModule::class
],
    declarations: [
    ProductController::class,
    TestController::class,
    ValuesController::class
],
    providers: [],
    /**
     * Bootstrap controller must contain an index() for bootstrap
     */
    bootstrap: [ValuesController::class]
)]
class TestModule
{
    /**
     * Method is called for app configuration
     * configure routing here
     * @param IApplicationBuilder $app
     * @param IWebHostEnvironment $env
     */
    public function configure(IApplicationBuilder $app, ?IWebHostEnvironment $env = null): void
    {
//        if ($env->isDevelopment()) {
//            $app->useDeveloperExceptionPage();
//        }

        $endpoints = new RouteBuilder();


        $app->useHttpsRedirection();

        $app->useRouting();

        $app->useAuthorization();

        $app->useEndpoints(
            function () use ($endpoints) {
                $endpoints->mapControllers();
            }
        );
    }


}