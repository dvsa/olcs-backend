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
    const TRANSPORT_MANAGER_STATUS_CURRENT = 'tm_s_cur';
    const TRANSPORT_MANAGER_STATUS_ACTIVE = 'tm_st_act';
    const TRANSPORT_MANAGER_STATUS_DISABLED = 'tm_st_disa';
    const TRANSPORT_MANAGER_TYPE_EXTERNAL = 'tm_t_e';
    const TRANSPORT_MANAGER_TYPE_BOTH = 'tm_t_b';

    public function updateTransportManager($type, $status, $workCd, $homeCd, $createdBy)
    {
        $this->setTmType($type);
        $this->setTmStatus($status);
        $this->setWorkCd($workCd);
        $this->setHomeCd($homeCd);
        $this->setCreatedBy($createdBy);
    }
}
