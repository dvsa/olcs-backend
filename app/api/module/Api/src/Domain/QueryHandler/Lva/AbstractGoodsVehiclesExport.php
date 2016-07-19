<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Lva;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Goods Vehicles
 *
 * @author Dmitrij Golubev <dmitrij.golubev@valtech.co.uk>
 */
abstract class AbstractGoodsVehiclesExport extends AbstractQueryHandler
{
    protected $repoServiceName = 'LicenceVehicle';

    /**
     * Get vehicle data from db
     *
     * @param QueryBuilder $qb Query Builder
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function getData(QueryBuilder $qb)
    {
        $result = $this->getRepo()->fetchPaginatedList($qb, Query::HYDRATE_OBJECT);

        return [
            'results' => $this->resultList(
                $result,
                [
                    'vehicle',
                    'goodsDiscs',
                    'interimApplication'
                ]
            ),
            'count' => count($result),
        ];
    }
}
