<?php

/**
 * WithPersonContactDetails Factory
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * WithPersonContactDetails Factory
 */
class WithPersonContactDetailsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WithPersonContactDetails(
            $serviceLocator->get('with')
        );
    }
}
