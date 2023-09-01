<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class NoOfPermitsAnswerSaverFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NoOfPermitsAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsAnswerSaver
    {
        return new NoOfPermitsAnswerSaver(
            $container->get('QaGenericAnswerFetcher'),
            $container->get('QaEcmtRemovalNoOfPermitsAnswerWriter'),
            $container->get('QaEcmtRemovalNoOfPermitsFeeCreator')
        );
    }
}
