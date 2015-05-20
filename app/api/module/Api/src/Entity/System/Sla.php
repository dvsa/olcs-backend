<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sla Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sla")
 */
class Sla extends AbstractSla
{
    public function appliesTo(\DateTime $date)
    {
        if ($this->effectiveFrom !== null && $this->effectiveFrom > $date) {
            return false;
        }

        if ($this->effectiveTo !== null && $this->effectiveTo < $date) {
            return false;
        }

        return true;
    }
}
