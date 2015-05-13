<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicenceStatusRule Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="licence_status_rule",
 *    indexes={
 *        @ORM\Index(name="ix_licence_status_rule_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_licence_status_rule_licence_status", columns={"licence_status"}),
 *        @ORM\Index(name="ix_licence_status_rule_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_status_rule_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class LicenceStatusRule extends AbstractLicenceStatusRule
{

}
