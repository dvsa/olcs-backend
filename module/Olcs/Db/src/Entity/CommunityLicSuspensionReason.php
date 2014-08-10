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
 *        @ORM\Index(name="fk_community_lic_suspension_reason_community_lic_suspension_idx", columns={"community_lic_suspension_id"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_community_lic_suspension_idx1", columns={"reason_id"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicSuspensionReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\CommunityLicSuspensionReasonType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicSuspensionReasonType")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id")
     */
    protected $reason;

    /**
     * Community lic suspension
     *
     * @var \Olcs\Db\Entity\CommunityLicSuspension
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicSuspension")
     * @ORM\JoinColumn(name="community_lic_suspension_id", referencedColumnName="id")
     */
    protected $communityLicSuspension;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

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
