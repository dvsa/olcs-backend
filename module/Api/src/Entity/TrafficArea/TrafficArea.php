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
    const NORTH_EASTERN_TRAFFIC_AREA_CODE    = 'B';
    const NORTH_WESTERN_TRAFFIC_AREA_CODE    = 'C';
    const WEST_MIDLANDS_TRAFFIC_AREA_CODE    = 'D';
    const EASTERN_TRAFFIC_AREA_CODE          = 'F';
    const WELSH_TRAFFIC_AREA_CODE            = 'G';
    const WESTERN_TRAFFIC_AREA_CODE          = 'H';
    const SE_MET_TRAFFIC_AREA_CODE           = 'K';
    const SCOTTISH_TRAFFIC_AREA_CODE         = 'M';
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';
}
