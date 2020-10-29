<?php

declare(strict_types=1);


namespace Spatial\Api;

use Spatial\Api\Controllers\ProductController;
use Spatial\Api\Controllers\TestController;
use Spatial\Api\Controllers\ValuesController;
use Spatial\Core\ApiModule;


#[ApiModule(
    imports: [
    Router::class
],
    declarations: [
    ProductController::class,
    TestController::class,
    ValuesController::class
],
    providers: [],
    bootstrap: [ValuesController::class]
)]
class AppModule
{
}