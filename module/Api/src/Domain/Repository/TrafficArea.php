<?php

/**
 * Traffic Area
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Traffic Area
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TrafficArea extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @return array
     */
    public function getValueOptions()
    {
        $qb = $this->createQueryBuilder();
        $results = $qb->getQuery()->getResult();

        $valueOptions = [];
        foreach ($results as $result) {
            if ($result->getId() == Entity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                continue;
            }
            $valueOptions[$result->getId()] = $result->getName();
        }

        asort($valueOptions);

        return $valueOptions;
    }
}
