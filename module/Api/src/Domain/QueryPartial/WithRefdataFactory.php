<?php

/**
 * With Refdata Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * With Refdata Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class WithRefdataFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WithRefdata(
            $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('with')
        );
    }
}
