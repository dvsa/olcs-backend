<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtCountryConstraintLink Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_country_constraint_link",
 *    indexes={
 *        @ORM\Index(name="ecmt_country_constraint_link_country_id", columns={"country_id"}),
 *        @ORM\Index(name="ecmt_country_constraint_link_constraint_id", columns={"constraint_id"}),
 *        @ORM\Index(name="ecmt_country_constraint_link_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ecmt_country_constraint_link_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class EcmtCountryConstraintLink extends AbstractEcmtCountryConstraintLink
{

}
