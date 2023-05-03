<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Publish an application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Publish extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\Application\PublishValidationService
     */
    private $applicationValidationService;

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\Variation\PublishValidationService
     */
    private $variationValidationService;


    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, Publish::class);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($query);

        $validationService = ($application->getIsVariation()) ?
            $this->variationValidationService :
            $this->applicationValidationService;

        return $this->result(
            $application,
            [],
            [
                'hasActiveS4' => $application->hasActiveS4(),
                'existingPublication' => !$application->getPublicationLinks()->isEmpty(),
                'errors' => $validationService->validate($application),
            ]
        );
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Publish
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->applicationValidationService = $container->get('ApplicationPublishValidationService');
        $this->variationValidationService = $container->get('VariationPublishValidationService');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
