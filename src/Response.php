<?php

namespace Spatial\Router;

use GuzzleHttp\Psr7\Response as GuzzleHttpResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * PSR-7 response implementation.
 */
class Response  extends GuzzleHttpResponse implements ResponseInterface
{
}
