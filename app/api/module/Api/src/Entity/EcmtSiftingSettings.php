<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtSiftingSettings Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_sifting_settings",
 *    indexes={
 *        @ORM\Index(name="ecmt_sifting_settings_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_sifting_settings_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class EcmtSiftingSettings extends AbstractEcmtSiftingSettings
{

}
