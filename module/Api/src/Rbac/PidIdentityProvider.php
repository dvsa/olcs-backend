<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Utils\Auth\AuthHelper;
use Zend\Http\Header\GenericHeader;
use ZfcRbac\Identity\IdentityProviderInterface;
use ZfcRbac\Identity\IdentityInterface;
use Zend\Http\Request;

/**
 * Identity Provider
 */
class PidIdentityProvider implements IdentityProviderInterface
{
    // @todo remove user from testdata when we find constant solution for CLI requests
    const SYSTEM_USER = 1;

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

    /**
     * @var Identity;
     */
    private $identity;

    public function __construct(RepositoryInterface $repository, $request, $headerName)
    {
        $this->repository = $repository;
        $this->request = $request;
        $this->headerName = $headerName;
    }

    private function authenticate()
    {
        if (AuthHelper::isOpenAm()) {

            $pid = $this->request->getHeader($this->headerName, new GenericHeader())->getFieldValue();

            if (!empty($pid)) {
                return $this->repository->fetchByPid($pid);
            }
        } elseif ($this->request instanceof \Zend\Http\Request) {
            // @todo remove once we are 100% using openAM
            $auth = $this->request->getHeader('Authorization', new GenericHeader())->getFieldValue();
            if (!empty($auth)) {
                return $this->repository->fetchById($auth);
            }
        } elseif ($this->request instanceof \Zend\Console\Request) {
            // @todo remove when we find constant solution for CLI requests
            $auth = self::SYSTEM_USER;
            return $this->repository->fetchById($auth);
        }

        return null;
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
}
