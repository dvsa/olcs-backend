<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CommunityLicWithdrawalReasonType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="community_lic_withdrawal_reason_type",
 *    indexes={
 *        @ORM\Index(name="fk_community_lic_withdrawal_reason_type_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_withdrawal_reason_type_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicWithdrawalReasonType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Deleted date
     *
     * @var string
     *
     * @ORM\Column(type="string", name="deleted_date", length=45, nullable=true)
     */
    protected $deletedDate;

    /**
     * Set the deleted date
     *
     * @param string $deletedDate
     * @return \Olcs\Db\Entity\CommunityLicWithdrawalReasonType
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return string
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }
}
