<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefData Entity
 *
 * @ORM\Entity(repositoryClass="Dvsa\Olcs\Api\Domain\Repository\RefData")
 * @ORM\Table(name="ref_data",
 *    indexes={
 *        @ORM\Index(name="ix_ref_data_parent_id", columns={"parent_id"}),
 *        @ORM\Index(name="ix_ref_data_ref_data_category_id", columns={"ref_data_category_id"})
 *    }
 * )
 */
class RefData extends AbstractRefData
{

}
