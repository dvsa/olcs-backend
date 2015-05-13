<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransportManager Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="transport_manager",
 *    indexes={
 *        @ORM\Index(name="ix_transport_manager_tm_status", columns={"tm_status"}),
 *        @ORM\Index(name="ix_transport_manager_tm_type", columns={"tm_type"}),
 *        @ORM\Index(name="ix_transport_manager_home_cd_id", columns={"home_cd_id"}),
 *        @ORM\Index(name="ix_transport_manager_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_transport_manager_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_transport_manager_work_cd_id", columns={"work_cd_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_transport_manager_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TransportManager extends AbstractTransportManager
{

}
