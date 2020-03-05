<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Qa\QaContextGenerator;
use Dvsa\Olcs\Transfer\Query\Qa\ApplicationStep as ApplicationStepQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->qaContextGenerator = $mainServiceLocator->get('QaContextGenerator');
        $this->selfservePageGenerator = $mainServiceLocator->get('QaSelfservePageGenerator');

        return parent::createService($serviceLocator);
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
}
