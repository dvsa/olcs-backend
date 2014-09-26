<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CommunityLicSuspensionReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="community_lic_suspension_reason",
 *    indexes={
 *        @ORM\Index(name="IDX_AAD53DD059BB1592", columns={"reason_id"}),
 *        @ORM\Index(name="IDX_AAD53DD010888A2E", columns={"community_lic_suspension_id"}),
 *        @ORM\Index(name="IDX_AAD53DD0DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_AAD53DD065CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicSuspensionReason implements Interfaces\EntityInterface
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
     * @var \Olcs\Db\Entity\CommunityLicSuspensionReasonType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicSuspensionReasonType", fetch="LAZY")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=false)
     */
    protected $reason;

    /**
     * Community lic suspension
     *
     * @var \Olcs\Db\Entity\CommunityLicSuspension
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicSuspension", fetch="LAZY")
     * @ORM\JoinColumn(name="community_lic_suspension_id", referencedColumnName="id", nullable=false)
     */
    protected $communityLicSuspension;

    /**
     * Set the reason
     *
     * @param \Olcs\Db\Entity\CommunityLicSuspensionReasonType $reason
     * @return CommunityLicSuspensionReason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Olcs\Db\Entity\CommunityLicSuspensionReasonType
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set the community lic suspension
     *
     * @param \Olcs\Db\Entity\CommunityLicSuspension $communityLicSuspension
     * @return CommunityLicSuspensionReason
     */
    public function setCommunityLicSuspension($communityLicSuspension)
    {
        $this->communityLicSuspension = $communityLicSuspension;

        return $this;
    }

    /**
     * Get the community lic suspension
     *
     * @return \Olcs\Db\Entity\CommunityLicSuspension
     */
    public function getCommunityLicSuspension()
    {
        return $this->communityLicSuspension;
    }
}
