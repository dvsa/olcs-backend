<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

class FailedRequestException extends \Exception
{
    private $response;

    public function __construct($response, $code = 0, \Exception $previous = null)
    {
        $this->response = $response;
        parent::__construct('Invalid response from OpenAm service', $code, $previous);
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}
