<?php

/**
 * With Refdata Factory
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * With Refdata Factory
 */
class WithUserFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WithUser(
            $serviceLocator->get('with')
        );
    }
}
