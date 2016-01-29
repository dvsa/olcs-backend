<?php

namespace Dvsa\Olcs\Api\Domain;

use ZfcRbac\Service\AuthorizationService;

/**
 * Auth Aware Interface
 */
interface AuthAwareInterface
{
    /**
     * @param AuthorizationService $service
     */
    public function setAuthService(AuthorizationService $service);

    /**
     * @return AuthorizationService
     */
    public function getAuthService();

    /**
     * @param $permission
     * @return bool
     */
    public function isGranted($permission);
}
