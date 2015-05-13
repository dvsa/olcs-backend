<?php

namespace Dvsa\Olcs\Api\Entity\TrafficArea;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrafficArea Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="traffic_area",
 *    indexes={
 *        @ORM\Index(name="ix_traffic_area_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_traffic_area_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_traffic_area_contact_details_id", columns={"contact_details_id"})
 *    }
 * )
 */
class TrafficArea extends AbstractTrafficArea
{

}
