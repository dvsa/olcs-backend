<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class AnswersSummaryGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswersSummaryGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AnswersSummaryGenerator
    {
        return $this->__invoke($serviceLocator, AnswersSummaryGenerator::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AnswersSummaryGenerator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnswersSummaryGenerator
    {
        $answersSummaryGenerator = new AnswersSummaryGenerator(
            $container->get('PermitsAnswersSummaryFactory'),
            $container->get('PermitsHeaderAnswersSummaryRowsAdder'),
            $container->get('QaAnswersSummaryRowsAdder')
        );
        $answersSummaryGenerator->registerCustomRowsAdder(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            $container->get('PermitsBilateralAnswersSummaryRowsAdder')
        );
        $answersSummaryGenerator->registerCustomRowsAdder(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
            $container->get('PermitsMultilateralAnswersSummaryRowsAdder')
        );
        return $answersSummaryGenerator;
    }
}
