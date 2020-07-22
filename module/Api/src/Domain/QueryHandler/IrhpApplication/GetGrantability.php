<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\GrantabilityChecker;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Gets grantability of IRHP Application
 */
class GetGrantability extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpApplication';

    /** @var GrantabilityChecker */
    private $grantabilityChecker;

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

        $this->grantabilityChecker = $mainServiceLocator->get('PermitsGrantabilityChecker');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle Query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplicationId = $query->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        $message = '';
        $grantable = 1;

        if (!$irhpApplication->canBeGranted()) {
            $grantable = 0;
            $message = 'IRHP Application can not be granted';
        } elseif (!$this->grantabilityChecker->isGrantable($irhpApplication)) {
            $grantable = 0;
            $message = 'Application requests too many permits from a range';
        }

        return [
            'grantable' => $grantable,
            'message' => $message
        ];
    }
}
