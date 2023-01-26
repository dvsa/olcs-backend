<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\EmissionsStandardsAnswerSaver;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class EmissionsStandardsQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FixedAnswerQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FixedAnswerQuestionHandler
    {
        return $this->__invoke($serviceLocator, FixedAnswerQuestionHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FixedAnswerQuestionHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FixedAnswerQuestionHandler
    {
        return new FixedAnswerQuestionHandler(
            $container->get('QaGenericAnswerWriter'),
            EmissionsStandardsAnswerSaver::EURO3_OR_EURO4_ANSWER
        );
    }
}
