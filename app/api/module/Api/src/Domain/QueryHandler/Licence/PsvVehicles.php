<?php

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

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
    protected $repoServiceName = 'Licence';

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
        /** @var Entity\Licence\Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        $flags = $this->helper->getCommonQueryFlags($licence, $query);

        $flags['canTransfer'] = !$licence->getOtherActiveLicences()->isEmpty();
        $flags['hasBreakdown'] = $licence->hasPsvBreakdown();

        return $this->result($licence, [], $flags);
    }
}
