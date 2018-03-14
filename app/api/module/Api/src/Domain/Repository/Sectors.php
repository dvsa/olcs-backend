<?php

/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Sectors as Entity;
use Dvsa\Olcs\Api\Entity\EcmtPermits as EcmtPermitsEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;


/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class Sectors extends AbstractRepository
{

    protected $entity = Entity::class;

    public function calculatePermitsNumber($siftingPercentage){

        /*
         *
         * 1224 permits
         * -5%
         * $totalPermits = 1162
         *
         * */

        $totalPermits = 1162 / 100;

        return round(($totalPermits * $siftingPercentage),0,PHP_ROUND_HALF_DOWN);
    }

    public function getApplicationsTotal($sectorId){

        $qbs = $this->getEntityManager()->createQueryBuilder()
          ->select('ep.permitsId')
          ->from(EcmtPermitsEntity::class,'ep')
          ->where('ep.sectorId = ' . $sectorId);
        return count($qbs->getQuery()->execute());
    }

}
