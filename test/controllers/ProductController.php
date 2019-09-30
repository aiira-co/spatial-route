<?php

namespace Spatial\Api\Controllers;

use Spatial\Psr7\Response;

/**
 * ValuesController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */

class ProductController
{
    /**
     * Use constructor to Inject or instanciate dependecies
     */
    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * The Method httpGet() called to handle a GET request
     * URI: POST: https://api.com/values
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpGet(int ...$id): Response
    {
        $data = [
            'app api',
            'value1',
            'value2',
            $id
        ];
        $payload = json_encode($data);

        $this->response->getBody()->write($payload);
        return $this->response;
        // ->withHeader('Content-Type', 'application/json');
        // ->withHeader('Content-Disposition', 'attachment;filename="downloaded.pdf"');
    }
}
