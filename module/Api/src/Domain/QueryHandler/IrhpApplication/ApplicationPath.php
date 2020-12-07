<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Qa\Structure\FormFragmentGenerator;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ApplicationPath as ApplicationPathQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->formFragmentGenerator = $mainServiceLocator->get('QaFormFragmentGenerator');

        return parent::createService($serviceLocator);
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
}
