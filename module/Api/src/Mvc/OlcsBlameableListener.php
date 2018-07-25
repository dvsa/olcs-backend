<?php
namespace Dvsa\Olcs\Api\Mvc;

use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Gedmo\Blameable\BlameableListener as GedmoBlameableListener;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
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

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

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
}
