<?php

/**
 * With Contact Details Factory
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * With Contact Details Factory
 */
class WithContactDetailsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WithContactDetails(
            $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('with'),
            $serviceLocator->get('withRefdata')
        );
    }
}
