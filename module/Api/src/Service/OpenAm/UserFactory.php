<?php
namespace Dvsa\Olcs\Api\Service\OpenAm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new User(
            $serviceLocator->get(ClientInterface::class),
            (new \RandomLib\Factory())->getMediumStrengthGenerator()
        );
    }
}
