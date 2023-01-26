<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IpaAnswersSummaryGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IpaAnswersSummaryGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IpaAnswersSummaryGenerator
    {
        return $this->__invoke($serviceLocator, IpaAnswersSummaryGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IpaAnswersSummaryGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IpaAnswersSummaryGenerator
    {
        $answersSummaryGenerator = new IpaAnswersSummaryGenerator(
            $container->get('PermitsAnswersSummaryFactory'),
            $container->get('QaAnswersSummaryRowsAdder')
        );
        $answersSummaryGenerator->registerCustomRowsAdder(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            $container->get('PermitsBilateralIpaAnswersSummaryRowsAdder')
        );
        return $answersSummaryGenerator;
    }
}
