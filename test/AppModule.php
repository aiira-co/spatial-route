<?php

declare(strict_types=1);


namespace Spatial\Api;

use Spatial\Api\Controllers\ProductController;
use Spatial\Api\Controllers\TestController;
use Spatial\Api\Controllers\ValuesController;
use Spatial\Core\ApiModule;
use Spatial\Router\Interface\IRouteBuilder;


#[ApiModule(
    imports: [
    RouterModule::class
],
    declarations: [
    ProductController::class,
    TestController::class,
    ValuesController::class
],
    providers: [],
    /**
     * Bootstrap contrroller must contain an index() for bootstrap
     */
    bootstrap: [ValuesController::class]
)]
class AppModule
{
    /**
     * Method is called for app configuration
     * configure routing here
     * @param $app
     */
    public function configure(IApplicationBuilder $app):void
    {
        $app.UseRoute('configureRoute');
    }
    private function configureRoute(IRouteBuilder $routeBuilder):void {
         //Home/Index
         $routeBuilder->mapRoute("Default", "{controller = Home}/{action = Index}/{id?}");
      }

}