<?php

namespace Dvsa\Olcs\Api\Entity\Irfo;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoTransitCountry Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irfo_transit_country",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_transit_country_irfo_psv_auth_id", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="ix_irfo_transit_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_transit_country_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_transit_country_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class IrfoTransitCountry extends AbstractIrfoTransitCountry
{

}
