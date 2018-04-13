<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtCountriesConstraints Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_countries_constraints",
 *    indexes={
 *        @ORM\Index(name="ecmt_ecmt_countries_constraints_created_by", columns={"created_by"})
 *    }
 * )
 */
class EcmtCountriesConstraints extends AbstractEcmtCountriesConstraints
{

}
