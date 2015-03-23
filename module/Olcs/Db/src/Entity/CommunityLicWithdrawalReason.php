<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CommunityLicWithdrawalReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="community_lic_withdrawal_reason",
 *    indexes={
 *        @ORM\Index(name="ix_community_lic_withdrawal_reason_community_lic_withdrawal_id", columns={"community_lic_withdrawal_id"}),
 *        @ORM\Index(name="ix_community_lic_withdrawal_reason_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_withdrawal_reason_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_community_lic_withdrawal_reason_ref_data1_idx", columns={"type_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_withdrawal_reason_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLicWithdrawalReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\TypeManyToOne,
        Traits\CustomVersionField;

    /**
     * Community lic withdrawal
     *
     * @var \Olcs\Db\Entity\CommunityLicWithdrawal
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicWithdrawal")
     * @ORM\JoinColumn(name="community_lic_withdrawal_id", referencedColumnName="id", nullable=false)
     */
    protected $communityLicWithdrawal;

    /**
     * Set the community lic withdrawal
     *
     * @param \Olcs\Db\Entity\CommunityLicWithdrawal $communityLicWithdrawal
     * @return CommunityLicWithdrawalReason
     */
    public function setCommunityLicWithdrawal($communityLicWithdrawal)
    {
        $this->communityLicWithdrawal = $communityLicWithdrawal;

        return $this;
    }

    /**
     * Get the community lic withdrawal
     *
     * @return \Olcs\Db\Entity\CommunityLicWithdrawal
     */
    public function getCommunityLicWithdrawal()
    {
        return $this->communityLicWithdrawal;
    }
}
