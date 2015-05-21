<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TmEmployment Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="tm_employment",
 *    indexes={
 *        @ORM\Index(name="ix_tm_employment_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_tm_employment_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="fk_tm_employment_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_employment_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TmEmployment extends AbstractTmEmployment
{

}
