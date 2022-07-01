<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumerServices;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\RefundInterimFees;
use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Refund Interim Fees Factory
 */
class RefundInterimFeesFactory implements FactoryInterface
{
    /**
     * Factory
     *
     * @param MessageConsumerManager $serviceLocator Manager
     *
     * @return RefundInterimFees
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Laminas\ServiceManager\ServiceManager $sl */
        $sl = $serviceLocator->getServiceLocator();

        return new RefundInterimFees(
            $sl->get(AbstractConsumerServices::class),
            $sl->get('RepositoryServiceManager')->get('Fee')
        );
    }
}
