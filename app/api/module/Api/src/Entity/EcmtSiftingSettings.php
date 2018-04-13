<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * EcmtSiftingSettings Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_sifting_settings",
 *    indexes={
 *        @ORM\Index(name="ecmt_ecmt_sifting_settings_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_ecmt_sifting_settings_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class EcmtSiftingSettings extends AbstractEcmtSiftingSettings
{
    public function update(DateTime $startDate, DateTime $endDate, $totalQuotaPermits)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalQuotaPermits = $totalQuotaPermits;
    }
}
