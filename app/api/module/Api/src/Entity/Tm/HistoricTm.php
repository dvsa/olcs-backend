<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoricTm Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="historic_tm",
 *    indexes={
 *        @ORM\Index(name="ix_historic_tm_historic_id", columns={"historic_id"}),
 *        @ORM\Index(name="ix_historic_tm_forename", columns={"forename"}),
 *        @ORM\Index(name="ix_historic_tm_family_name", columns={"family_name"}),
 *        @ORM\Index(name="ix_historic_tm_lic_no", columns={"lic_no"}),
 *        @ORM\Index(name="ix_historic_tm_birth_date", columns={"birth_date"})
 *    }
 * )
 */
class HistoricTm extends AbstractHistoricTm
{
}
