<?php

namespace Dvsa\Olcs\Api\Domain;

use LmcRbacMvc\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepoService;

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
     * @param UserRepoService $service
     */
    public function setUserRepository(UserRepoService $service);

    /**
     * @return UserRepoService
     */
    public function getUserRepository();

    /**
     * @param $permission
     * @return bool
     */
    public function isGranted($permission);
}
