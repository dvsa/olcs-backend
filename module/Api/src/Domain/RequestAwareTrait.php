<?php

namespace Dvsa\Olcs\Api\Domain;

use Laminas\Http\Request;

trait RequestAwareTrait
{
    protected Request $request;

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
