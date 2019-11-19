<?php

namespace Dvsa\Olcs\DocumentShare\Service;

/**
 * Class WebDavResponse
 *
 * @package Dvsa\Olcs\DocumentShare\Service
 */
class WebDavResponse
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
