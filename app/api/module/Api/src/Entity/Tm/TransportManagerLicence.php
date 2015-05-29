<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransportManagerLicence Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="transport_manager_licence",
 *    indexes={
 *        @ORM\Index(name="ix_transport_manager_licence_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_transport_manager_licence_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_transport_manager_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_transport_manager_licence_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_transport_manager_licence_tm_type", columns={"tm_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_transport_manager_licence_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TransportManagerLicence extends AbstractTransportManagerLicence
{

}
