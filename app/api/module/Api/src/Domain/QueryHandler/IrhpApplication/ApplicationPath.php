<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Qa\Structure\FormFragmentGenerator;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ApplicationPath as ApplicationPathQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Application path
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationPath extends AbstractQueryHandler
{
    /** @var FormFragmentGenerator */
    private $formFragmentGenerator;

    protected $repoServiceName = 'IrhpApplication';

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
        return $this->__invoke($serviceLocator, ApplicationPath::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|ApplicationPathQry $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpApplication = $this->getRepo()->fetchUsingId($query);
        $applicationPath = $irhpApplication->getActiveApplicationPath();

        $formFragment = $this->formFragmentGenerator->generate(
            $applicationPath->getApplicationSteps()->getValues(),
            $irhpApplication
        );

        return $formFragment->getRepresentation();
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ApplicationPath
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

        $this->formFragmentGenerator = $container->get('QaFormFragmentGenerator');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
