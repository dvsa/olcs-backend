<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlaTargetDate Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sla_target_date",
 *    indexes={
 *        @ORM\Index(name="ix_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_country_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_sla_target_date_document_id", columns={"document_id"})
 *    }
 * )
 */
class SlaTargetDate extends AbstractSlaTargetDate
{

}
