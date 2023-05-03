<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Qa\QaContextGenerator;
use Dvsa\Olcs\Transfer\Query\Qa\ApplicationStep as ApplicationStepQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Application step
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationStep extends AbstractQueryHandler
{
    /** @var QaContextGenerator */
    private $qaContextGenerator;

    /** @var SelfservePageGenerator */
    private $selfservePageGenerator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, ApplicationStep::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|ApplicationStepQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $qaContext = $this->qaContextGenerator->generate(
            $query->getId(),
            $query->getIrhpPermitApplication(),
            $query->getSlug()
        );

        $selfservePage = $this->selfservePageGenerator->generate($qaContext);
        return $selfservePage->getRepresentation();
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationStep
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->qaContextGenerator = $container->get('QaContextGenerator');
        $this->selfservePageGenerator = $container->get('QaSelfservePageGenerator');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
