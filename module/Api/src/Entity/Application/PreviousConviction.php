<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\ORM\Mapping as ORM;

/**
 * PreviousConviction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="previous_conviction",
 *    indexes={
 *        @ORM\Index(name="ix_previous_conviction_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_previous_conviction_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_previous_conviction_title", columns={"title"})
 *    }
 * )
 */
class PreviousConviction extends AbstractPreviousConviction
{

}
