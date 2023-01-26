<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class QuestionTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return QuestionTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): QuestionTextGenerator
    {
        return $this->__invoke($serviceLocator, QuestionTextGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return QuestionTextGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QuestionTextGenerator
    {
        return new QuestionTextGenerator(
            $container->get('QaQuestionTextFactory'),
            $container->get('QaJsonDecodingFilteredTranslateableTextGenerator')
        );
    }
}
