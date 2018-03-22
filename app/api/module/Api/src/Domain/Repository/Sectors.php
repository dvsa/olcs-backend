<?php

/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Sectors as Entity;
use Dvsa\Olcs\Api\Entity\EcmtPermits as EcmtPermitsEntity;

/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class Sectors extends AbstractRepository
{

    protected $entity = Entity::class;

    public function calculatePermitsNumber($siftingPercentage,$totalPermitsNum,$retention){
        $availablePermits = round($totalPermitsNum - ($totalPermitsNum / 100 * $retention),0,PHP_ROUND_HALF_DOWN);
        $totalPermits = $availablePermits / 100;
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
