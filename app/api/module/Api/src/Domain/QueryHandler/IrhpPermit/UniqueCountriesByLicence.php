<?php

/**
 * Unique countries by licence
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UniqueCountriesByLicence extends AbstractQueryHandler
{
    /** @var QueryHandlerManager */
    private $queryHandlerManager;

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

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return UniqueCountriesByLicence
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;

        $this->queryHandlerManager = $container->get('QueryHandlerManager');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
