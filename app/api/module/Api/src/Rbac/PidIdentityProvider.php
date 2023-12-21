<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity\User\User;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Request;
use LmcRbacMvc\Identity\IdentityInterface;

/**
 * Identity Provider
 */
class PidIdentityProvider implements IdentityProviderInterface
{
    use IdentityProviderTrait;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $headerName;

    private string $cookieName;

    /**
     * @var Identity;
     */
    private $identity;

    public function __construct(RepositoryInterface $repository, $request, $headerName, $cookieName)
    {
        $this->repository = $repository;
        $this->request = $request;
        $this->headerName = $headerName;
        $this->cookieName = $cookieName;
    }

    /**
     * Get the identity
     *
     * @return IdentityInterface|null
     */
    public function getIdentity()
    {
        if ($this->identity === null) {
            $user = $this->authenticate();
            if ($user === null) {
                $this->identity = new Identity(User::anon());
            } else {
                $this->identity = new Identity($user);
            }
        }

        return $this->identity;
    }

    /**
     * @return User|null
     */
    private function authenticate()
    {
        if ($this->request instanceof \Laminas\Console\Request) {
            $auth = IdentityProviderInterface::SYSTEM_USER;
            return $this->repository->fetchById($auth);
        } else {
            $pid = $this->request->getHeader($this->headerName, new GenericHeader())->getFieldValue();

            if (!empty($pid)) {
                return $this->repository->fetchByPid($pid);
            }
        }

        return null;
    }

    /**
     * get the token
     */
    public function getToken(): ?string
    {
        $cookie = $this->getCookie();

        if (empty($cookie->{$this->cookieName})) {
            return null;
        }

        return $cookie->{$this->cookieName};
    }

    private function getCookie()
    {
        return $this->request->getHeaders()->get('Cookie');
    }
}
