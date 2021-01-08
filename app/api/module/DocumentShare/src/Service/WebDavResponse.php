<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Laminas\Http\Response;

/**
 * Class WebDavResponse
 *
 * @package Dvsa\Olcs\DocumentShare\Service
 */
class WebDavResponse extends Response
{
    /**
     * @var bool response
     */
    private $response;

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->response;
    }

    /**
     * @param bool $response
     */
    public function setResponse(bool $response)
    {
        $this->response = $response;
    }
}
