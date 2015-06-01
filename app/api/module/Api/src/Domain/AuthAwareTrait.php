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
     * @param $permission
     * @return bool
     */
    public function isGranted($permission, $context = null)
    {
        return $this->authService->isGranted($permission, $context);
    }
}
