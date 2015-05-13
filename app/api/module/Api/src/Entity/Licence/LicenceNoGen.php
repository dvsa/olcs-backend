<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicenceNoGen Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="licence_no_gen",
 *    indexes={
 *        @ORM\Index(name="ix_licence_no_gen_licence_id", columns={"licence_id"})
 *    }
 * )
 */
class LicenceNoGen extends AbstractLicenceNoGen
{

}
