<?php

namespace Dvsa\Olcs\Api\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Entity;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class OlcsEntityListener implements EventSubscriber, AuthAwareInterface, FactoryInterface
{
    use AuthAwareTrait;

    /** @var  ServiceLocatorInterface */
    private $sl;

    /**
     * Create instance
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->sl = $serviceLocator;

        return $this;
    }

    /**
     * Specifies the list of events to listen
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'preSoftDelete',
        ];
    }

    /**
     * Process event - preSoftDelete
     *
     * @param LifecycleEventArgs $args Event arguments
     *
     * @return void
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $om = $args->getEntityManager();

        $meta = $om->getClassMetadata(get_class($object));

        $this->updateField($object, $om, $meta, 'lastModifiedBy', $this->getModifiedByUser());
    }

    /**
     * Updates a field
     *
     * @param \Dvsa\Olcs\Api\Entity\*             $object Entity
     * @param \Doctrine\ORM\EntityManager         $om     Entity Manager
     * @param \Doctrine\ORM\Mapping\ClassMetadata $meta   Class meta data
     * @param string                              $field  Field Name
     * @param mixed                               $value  New value
     *
     * @return void
     */
    private function updateField($object, $om, $meta, $field, $value)
    {
        if (!method_exists($object, 'set' . ucfirst($field))) {
            return;
        }

        $property = $meta->getReflectionProperty($field);
        $oldValue = $property->getValue($object);

        if ($meta->hasAssociation($field) && $value) {
            $om->persist($value);
        }
        $property->setValue($object, $value);

        $uow = $om->getUnitOfWork();
        if ($object instanceof NotifyPropertyChanged) {
            $uow->propertyChanged($object, $field, $oldValue, $value);
        }

        $uow->scheduleExtraUpdate(
            $object,
            [
                $field => [$oldValue, $value],
            ]
        );
    }

    /**
     * Get the user value
     *
     * @return Entity\User\User|null
     */
    private function getModifiedByUser()
    {
        if ($this->getAuthService() === null) {
            $this->setAuthService($this->sl->get(AuthorizationService::class));
        }
        if ($this->getUserRepository() === null) {
            $this->setUserRepository($this->sl->get('RepositoryServiceManager')->get('User'));
        }

        $masqueradedAsSystemUser = $this->sl->get(PidIdentityProvider::class)->getMasqueradedAsSystemUser();

        if ($masqueradedAsSystemUser) {
            $currentUser = $this->getSystemUser();
        } else {
            $currentUser = $this->getCurrentUser();
        }

        return (
            (
                $currentUser instanceof Entity\User\User
                && $currentUser->isAnonymous() === false
            )
            ? $currentUser
            : null
        );
    }
}
