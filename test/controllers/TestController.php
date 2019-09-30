<?php

namespace Spatial\Api\Controllers;

use Core\Application\Logics\App\Queries\GetPersons;
use Core\Application\Logics\App\Command\CreatePerson;
use Core\Application\Logics\App\Command\UpdatePerson;
use Core\Application\Logics\App\Command\DeletePerson;
use Spatial\MediatR\Mediator;
use Spatial\Psr7\Response;

/**
 * ValuesController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */

class TestController
{
    /**
     * Use constructor to Inject or instanciate dependecies
     */
    public function __construct()
    {
        $this->mediator = new Mediator();
    }

    /**
     * The Method httpGet() called to handle a GET request
     * URI: POST: https://api.com/values
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpGet(int ...$id): ?Response
    {
        // mediator library. http server middleware
        // returns a response
        return $this->mediator->process(new GetPersonsQuery());
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var array $data
     * URI: POST: https://api.com/values
     */
    public function httpPost(array $data): Response
    {
        $r = new CreatePerson();
        $r->data = $data;
        return $this->mediator->process($r);
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var array $form and
     * An id as part of the uri.
     * URI: POST: https://api.com/values/2 the number 2 in the uri is passed as int $id to the method
     */
    public function httpPut(array $form, int $id): Response
    {

        // code here
        $r = new UpdatePerson();
        $r->data = $form;
        $r->id = $id;
        return $this->mediator->process($r);
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpDelete(int $id): Response
    {
        // code here
        $r = new DeletePerson();
        $r->id = $id;
        return $this->mediator->process($r);
    }
}
