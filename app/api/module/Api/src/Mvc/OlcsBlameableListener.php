<?php
namespace Dvsa\Olcs\Api\Mvc;

use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Gedmo\Blameable\BlameableListener as GedmoBlameableListener;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

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
     * {@inheritdoc}
     */
    public function getFieldValue($meta, $field, $eventAdapter)
    {
        $this->setUserValue(
            $this->getUserValue()
        );

        return $this->callParentGetFieldValue($meta, $field, $eventAdapter);
    }

    /**
     * Get the user value to set on a blameable field
     *
     * @return UserEntity|null
     */
    protected function getUserValue()
    {
        $serviceLocator = $this->getServiceLocator();
        if (!$this->getAuthService()) {
            // set the Auth Service, if not yet set
            $this->setAuthService($serviceLocator->get(AuthorizationService::class));
        }
        if (!$this->getUserRepository()) {
            // set the User repository service, if not yet set
            $this->setUserRepository($serviceLocator->get('RepositoryServiceManager')->get('User'));
        }

        $masquaradedAsSystemUser = $serviceLocator->get(PidIdentityProvider::class)->getMasqueradedAsSystemUser();
        if ($masquaradedAsSystemUser) {
            $currentUser = $this->getSystemUser();
        } else {
            $currentUser = $this->getCurrentUser();
        }

        return (($currentUser instanceof UserEntity) && !$currentUser->isAnonymous()) ? $currentUser : null;
    }

    /**
     * Call getFieldValue of the parent class - to facilitate unit testing
     *
     * @param object $meta
     * @param string $field
     * @param mixed $eventAdapter
     *
     * @return mixed
     */
    protected function callParentGetFieldValue($meta, $field, $eventAdapter)
    {
        return parent::getFieldValue($meta, $field, $eventAdapter);
    }
}
