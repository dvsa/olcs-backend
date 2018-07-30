<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtCountriesConstraints Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_countries_constraints",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_countries_constraints_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_countries_constraints_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class EcmtCountriesConstraints extends AbstractEcmtCountriesConstraints
{

}
