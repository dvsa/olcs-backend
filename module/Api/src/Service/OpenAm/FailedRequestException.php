<?php

/**
 * Failed Request Exception
 */

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Laminas\Http\Response;

/**
 * Failed Request Exception
 */
class FailedRequestException extends \Exception
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @param Response   $response response
     * @param int        $code     code
     * @param \Exception $previous previous
     */
    public function __construct($response, $code = 0, \Exception $previous = null)
    {
        $this->response = $response;
        parent::__construct('Invalid response from OpenAm service: ' . $response->getContent(), $code, $previous);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
