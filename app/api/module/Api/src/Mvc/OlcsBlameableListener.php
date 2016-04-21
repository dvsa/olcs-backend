<?php
namespace Dvsa\Olcs\Api\Mvc;

use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Gedmo\Blameable\BlameableListener as GedmoBlameableListener;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class OlcsBlameableListener
 *
 * @package Olcs\Api\Mvc
 */
class OlcsBlameableListener extends GedmoBlameableListener implements AuthAwareInterface
{
    use AuthAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * Get the user value to set on a blameable field
     *
     * @param object $meta
     * @param string $field
     *
     * @return UserEntity
     */
    public function getUserValue($meta, $field)
    {
        if (!$this->getAuthService()) {
            // set the Auth Service, if not yet set
            $this->setAuthService($this->getServiceLocator()->get(AuthorizationService::class));
        }

        // get the current user
        $currentUser = $this->getCurrentUser();

        return (($currentUser instanceof UserEntity) && !$currentUser->isAnonymous()) ? $currentUser : null;
    }
}
