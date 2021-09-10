<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Olcs\Api\Domain\Exception\NotImplementedException;
use Dvsa\Olcs\Api\Entity\User\User;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\Http\Request as HttpRequest;
use Laminas\Stdlib\RequestInterface;

class BlendedIdentityProvider implements IdentityProviderInterface
{
    use IdentityProviderTrait;

    /**
     * @var Identity
     */
    private $identity;

    /**
     * @var ConsoleRequest|HttpRequest
     */
    private $request;

    /**
     * @var JWTIdentityProvider
     */
    private $jwtIdentityProvider;

    /**
     * @var PidIdentityProvider
     */
    private $pidIdentityProvider;

    public function __construct(RequestInterface $request, JWTIdentityProvider $jwtIdentityProvider, PidIdentityProvider $pidIdentityProvider)
    {
        $this->request = $request;
        $this->jwtIdentityProvider = $jwtIdentityProvider;
        $this->pidIdentityProvider = $pidIdentityProvider;
    }

    /**
     * @inheritDoc
     */
    public function getIdentity()
    {
        if ($this->request instanceof ConsoleRequest) {
            return $this->jwtIdentityProvider->getIdentity();
        }

        if ($this->request->getHeaders()->has($this->pidIdentityProvider->getHeaderName())) {
            return $this->pidIdentityProvider->getIdentity();
        }

        if ($this->request->getHeaders()->has($this->jwtIdentityProvider->getHeaderName())) {
            return $this->jwtIdentityProvider->getIdentity();
        }

        return new Identity(User::anon());
    }

    /**
     * @return string
     * @throws NotImplementedException
     */
    public function getHeaderName(): string
    {
        throw new NotImplementedException();
    }
}
