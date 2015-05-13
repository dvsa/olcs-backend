<?php

namespace Dvsa\Olcs\Api\Entity\OperatingCentre;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcComplaint Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="oc_complaint",
 *    indexes={
 *        @ORM\Index(name="ix_oc_complaint_complaint_id", columns={"complaint_id"}),
 *        @ORM\Index(name="ix_oc_complaint_operating_centre_id", columns={"operating_centre_id"})
 *    }
 * )
 */
class OcComplaint extends AbstractOcComplaint
{

}
