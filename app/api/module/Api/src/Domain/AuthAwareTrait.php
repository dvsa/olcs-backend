<?php

namespace Dvsa\Olcs\Api\Domain;

use ZfcRbac\Service\AuthorizationService;

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
     * Does the current user have the External user role
     *
     * @return bool
     */
    public function isExternalUser()
    {
        return ($this->isGranted(\Dvsa\Olcs\Api\Entity\User\Permission::SELFSERVE_USER));
    }
}
