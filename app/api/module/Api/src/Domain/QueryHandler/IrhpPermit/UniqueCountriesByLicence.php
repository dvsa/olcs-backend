<?php

/**
 * Unique countries by licence
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UniqueCountriesByLicence extends AbstractQueryHandler
{
    /** @var QueryHandlerManager */
    private $queryHandlerManager;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->queryHandlerManager = $serviceLocator->getServiceLocator()->get('QueryHandlerManager');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        if ($query->getIrhpPermitType() != IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL) {
            return [];
        }

        $getListByLicenceQuery = GetListByLicence::create(
            $query->getArrayCopy()
        );

        $response = $this->queryHandlerManager->handleQuery($getListByLicenceQuery, false);

        $countryMap = [];
        foreach ($response['results'] as $irhpPermit) {
            $country = $irhpPermit['irhpPermitRange']['irhpPermitStock']['country'];

            $countryId = $country['id'];
            $countryName = $country['countryDesc'];
            $countryMap[$countryId] = $countryName;
        }

        asort($countryMap);

        return $countryMap;
    }
}
