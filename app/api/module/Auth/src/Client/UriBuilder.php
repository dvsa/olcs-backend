<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

class UriBuilder
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var null|string
     */
    private $realm;

    public function __construct(string $baseUrl, ?string $realm = null)
    {
        $this->baseUrl = $baseUrl;
        $this->realm = $realm;
    }

    public function setRealm(string $realm)
    {
        $this->realm = $realm;
    }

    /**
     * Build a full uri, including the baseUrl, $uri and optionally the realm
     *
     * @param string $uri URI
     *
     * @return string
     */
    public function build(string $uri): string
    {
        $fullUri = sprintf('%s/%s', rtrim($this->baseUrl, '/'), ltrim($uri, '/'));

        if (!empty($this->realm)) {
            $joinChar = strstr($fullUri, '?') ? '&' : '?';
            $fullUri .= $joinChar . 'realm=' . $this->realm;
        }

        return $fullUri;
    }
}
