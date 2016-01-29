<?php

/**
 * With Case Factory
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * With Case Factory
 */
class WithCaseFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WithCase(
            $serviceLocator->get('with')
        );
    }
}
