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
 *        @ORM\Index(name="ix_community_lic_suspension_reason_community_lic_suspension_id", columns={"community_lic_suspension_id"}),
 *        @ORM\Index(name="ix_community_lic_suspension_reason_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_suspension_reason_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_community_lic_suspension_reason_ref_data1_idx", columns={"type_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_suspension_reason_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLicSuspensionReason implements Interfaces\EntityInterface
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
     * Community lic suspension
     *
     * @var \Olcs\Db\Entity\CommunityLicSuspension
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLicSuspension")
     * @ORM\JoinColumn(name="community_lic_suspension_id", referencedColumnName="id", nullable=false)
     */
    protected $communityLicSuspension;

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
