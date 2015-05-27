<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Olcs\Api\Entity\User\User;
use ZfcRbac\Identity\IdentityProviderInterface;
use ZfcRbac\Identity\IdentityInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Request;

/**
 * Identity Provider
 *
 * @todo This is a temporary implementation of Rbac
 */
class IdentityProvider implements IdentityProviderInterface, FactoryInterface
{
    protected $identity;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Request $request */
        $request = $serviceLocator->get('Request');
        $auth = $request->getHeader('Authorization');

        if ($auth !== null) {
            $userId = $auth->getFieldValue();

            $userRepository = $serviceLocator->get('RepositoryServiceManager')->get('User');
            $user = $userRepository->fetchById($userId);

            if ($user instanceof User) {
                $this->setUser($user);
            }
        }

        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the identity
     *
     * @return IdentityInterface|null
     */
    public function getIdentity()
    {
        if ($this->identity === null) {
            if ($this->user === null) {
                return null;
            }

            $this->identity = new Identity();
            $this->identity->setUser($this->user);
        }

        return $this->identity;
    }
}
