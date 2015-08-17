<?php

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Service\PsvVehicles\PsvVehiclesQueryHelper;

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehicles extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var PsvVehiclesQueryHelper
     */
    protected $helper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->helper = $serviceLocator->getServiceLocator()->get('PsvVehiclesQueryHelper');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity\Application\Application $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $flags = $this->helper->getCommonQueryFlags($application, $query);

        $flags['canTransfer'] = false;
        $flags['hasBreakdown'] = $this->hasBreakdown($application);

        return $this->result($application, [], $flags);
    }

    protected function hasBreakdown(Entity\Application\Application $application)
    {
        return false;
    }
}
