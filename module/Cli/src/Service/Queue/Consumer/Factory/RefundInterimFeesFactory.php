<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumerServices;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\RefundInterimFees;
use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator): RefundInterimFees
    {
        return $this->__invoke($serviceLocator, RefundInterimFees::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RefundInterimFees
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefundInterimFees
    {
        /** @var \Laminas\ServiceManager\ServiceManager $sl */
        $sl = $container->getServiceLocator();
        return new RefundInterimFees(
            $sl->get(AbstractConsumerServices::class),
            $sl->get('RepositoryServiceManager')->get('Fee')
        );
    }
}
