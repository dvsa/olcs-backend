<?php

namespace Dvsa\Olcs\Api\Service\GovUkAccount\Response;

class GetAuthorisationUrlResponse
{
    private string $url;
    private string $state;
    private string $nonce;

    public function  __construct(string $url, string $state, string $nonce)
    {
        $this->url = $url;
        $this->state = $state;
        $this->nonce = $nonce;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }
}
