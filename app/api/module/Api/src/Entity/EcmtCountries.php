<?php

namespace Dvsa\Olcs\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtCountries Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_countries",
 *    indexes={
 *        @ORM\Index(name="ecmt_ecmt_countries_created_by", columns={"created_by"})
 *    }
 * )
 */
class EcmtCountries extends AbstractEcmtCountries
{

}
