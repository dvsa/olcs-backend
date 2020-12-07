<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Entity\User\User;
use Laminas\Http\Header\GenericHeader;
use ZfcRbac\Identity\IdentityProviderInterface;
use ZfcRbac\Identity\IdentityInterface;
use Laminas\Http\Request;

/**
 * Identity Provider
 */
class PidIdentityProvider implements IdentityProviderInterface
{
    const SYSTEM_USER = 1;
    const SYSTEM_TEAM = 1;

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

    /**
     * @var bool
     */
    private $masqueradedAsSystemUser;

    public function __construct(RepositoryInterface $repository, $request, $headerName)
    {
        $this->repository = $repository;
        $this->request = $request;
        $this->headerName = $headerName;
    }

    private function authenticate()
    {
        if ($this->request instanceof \Laminas\Console\Request) {
            $auth = self::SYSTEM_USER;
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
     * Get masqueraded as system user flag
     *
     * @return bool
     */
    public function getMasqueradedAsSystemUser()
    {
        return $this->masqueradedAsSystemUser;
    }

    /**
     * Set masqueraded as system user flag
     *
     * @param $masqueradedAsSystemUser
     */
    public function setMasqueradedAsSystemUser($masqueradedAsSystemUser)
    {
        $this->masqueradedAsSystemUser = $masqueradedAsSystemUser;
    }
}
