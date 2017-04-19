<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepoService;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * Auth Aware
 */
trait AuthAwareTrait
{
    /**
     * @var AuthorizationService
     */
    protected $authService;

    /**
     * @var UserRepoService
     */
    protected $repository;

    /**
     * @param AuthorizationService $service
     */
    public function setAuthService(AuthorizationService $service)
    {
        $this->authService = $service;
    }

    /**
     * @return AuthorizationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * @param UserRepoService $service
     */
    public function setUserRepository(UserRepoService $service)
    {
        $this->repository = $service;
    }

    /**
     * @return UserRepoService
     */
    public function getUserRepository()
    {
        return $this->repository;
    }

    /**
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCurrentUser()
    {
        $identity = $this->authService->getIdentity();

        if ($identity) {
            return $identity->getUser();
        }
    }

    /**
     * @note Even though this appears to be a one to one relationship, there is only ever one organisation for a user
     *
     * @return \Dvsa\Olcs\Api\Entity\Organisation\Organisation
     */
    public function getCurrentOrganisation()
    {
        $identity = $this->authService->getIdentity();

        $hasOrganisation = !$identity->getUser()->getOrganisationUsers()->isEmpty();

        if ($identity && $hasOrganisation) {
            return $identity->getUser()->getRelatedOrganisation();
        }
    }

    /**
     * @note Even though this appears to be a one to one relationship, there's only ever one local authority for a user
     * @todo olcs-14494 emergency fix, need to clean this up
     *
     * @return LocalAuthority
     */
    public function getCurrentLocalAuthority()
    {
        $identity = $this->authService->getIdentity();

        $localAuthority = $identity->getUser()->getLocalAuthority();

        if ($localAuthority instanceof LocalAuthority) {
            return $localAuthority;
        }
    }

    /**
     * @param $permission
     * @return bool
     */
    public function isGranted($permission, $context = null)
    {
        return $this->authService->isGranted($permission, $context);
    }

    /**
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getUser()
    {
        return $this->authService->getIdentity()->getUser();
    }

    /**
     * Does the current user have the Internal user role
     *
     * @return bool
     */
    public function isInternalUser()
    {
        return ($this->isGranted(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER));
    }

    /**
     * Is the current user a system user
     *
     * @return bool
     */
    public function isSystemUser()
    {
        return ($this->getCurrentUser() && $this->getCurrentUser()->isSystemUser());
    }

    /**
     * Does the current user have the External user role
     *
     * @return bool
     */
    public function isExternalUser()
    {
        return ($this->isGranted(\Dvsa\Olcs\Api\Entity\User\Permission::SELFSERVE_USER));
    }

    /**
     * Is the current user an anonymous user
     *
     * @return bool
     */
    public function isAnonymousUser()
    {
        return ($this->getCurrentUser()->isAnonymous());
    }

    /**
     * Does the current user have a Local Authority user role
     *
     * @return bool
     */
    public function isLocalAuthority()
    {
        return (
            $this->isGranted(\Dvsa\Olcs\Api\Entity\User\Permission::LOCAL_AUTHORITY_USER) ||
            $this->isGranted(\Dvsa\Olcs\Api\Entity\User\Permission::LOCAL_AUTHORITY_ADMIN)
        );
    }

    /**
     * Does the current user have an Operator user role
     *
     * @return bool
     */
    public function isOperator()
    {
        return (
            $this->isGranted(\Dvsa\Olcs\Api\Entity\User\Permission::OPERATOR_ADMIN) ||
            $this->isGranted(\Dvsa\Olcs\Api\Entity\User\Permission::OPERATOR_USER)
        );
    }

    /**
     * Get system user
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getSystemUser()
    {
        return $this->getUserRepository()->fetchById(PidIdentityProvider::SYSTEM_USER);
    }
}
