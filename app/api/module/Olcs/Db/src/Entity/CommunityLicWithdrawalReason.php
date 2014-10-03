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
 *        @ORM\Index(name="IDX_C7ACB34F59BB1592", columns={"reason_id"}),
 *        @ORM\Index(name="IDX_C7ACB34F24AA3C9B", columns={"community_lic_withdrawal_id"}),
 *        @ORM\Index(name="IDX_C7ACB34FDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_C7ACB34F65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicWithdrawalReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\CommunityLicWithdrawalReasonType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicWithdrawalReasonType", fetch="LAZY")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false)
     */
    protected $reason;

    /**
     * Community lic withdrawal
     *
     * @var \Olcs\Db\Entity\CommunityLicWithdrawal
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicWithdrawal", fetch="LAZY")
     * @ORM\JoinColumn(name="community_lic_withdrawal_id", referencedColumnName="id", nullable=false)
     */
    protected $communityLicWithdrawal;

    /**
     * Set the reason
     *
     * @param \Olcs\Db\Entity\CommunityLicWithdrawalReasonType $reason
     * @return CommunityLicWithdrawalReason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Olcs\Db\Entity\CommunityLicWithdrawalReasonType
     */
    public function getReason()
    {
        return $this->reason;
    }

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
