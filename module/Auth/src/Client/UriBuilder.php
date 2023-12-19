<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;

class UriBuilder
{
    public const MSG_REALM_NOT_SET = "'setRealm()' must be called before calling 'build()'";
    public const MSG_REALM_INCORRECT = "Invalid realm. Must be 'selfserve' or 'internal'";
    /**
     * @var string
     */
    private $internalUrl;

    /**
     * @var string
     */
    private $selfserveUrl;

    /**
     * @var null|string
     */
    private $realm;

    public function __construct(string $internalUrl, string $selfserveUrl, ?string $realm = null)
    {
        $this->internalUrl = $internalUrl;
        $this->selfserveUrl = $selfserveUrl;
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
        $fullUri = sprintf('%s/%s', rtrim($this->getBaseUrl(), '/'), ltrim($uri, '/'));

        $joinChar = strstr($fullUri, '?') ? '&' : '?';
        $fullUri .= $joinChar . 'realm=' . $this->realm;

        return $fullUri;
    }

    protected function getBaseUrl()
    {
        if (is_null($this->realm)) {
            throw new ClientException(static::MSG_REALM_NOT_SET);
        }

        switch ($this->realm) {
            case 'internal':
                return $this->internalUrl;
            case 'selfserve':
                return $this->selfserveUrl;
            default:
                throw new ClientException(static::MSG_REALM_INCORRECT);
        }
    }
}
