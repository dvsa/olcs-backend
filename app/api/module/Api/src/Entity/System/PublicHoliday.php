<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicHoliday Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="public_holiday",
 *    indexes={
 *        @ORM\Index(name="ix_public_holiday_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_public_holiday_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class PublicHoliday extends AbstractPublicHoliday
{

}
