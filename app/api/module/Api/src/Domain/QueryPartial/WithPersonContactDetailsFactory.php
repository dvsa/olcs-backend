<?php

/**
 * WithPersonContactDetails Factory
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
