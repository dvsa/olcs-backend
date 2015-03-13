<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CommunityLicSuspension Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="community_lic_suspension",
 *    indexes={
 *        @ORM\Index(name="ix_community_lic_suspension_community_lic_id", columns={"community_lic_id"}),
 *        @ORM\Index(name="ix_community_lic_suspension_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_suspension_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_suspension_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLicSuspension implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CommunityLicManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\EndDateFieldAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\StartDateField,
        Traits\CustomVersionField;

    /**
     * Is actioned
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_actioned", nullable=true, options={"default": 0})
     */
    protected $isActioned = 0;

    /**
     * Set the is actioned
     *
     * @param string $isActioned
     * @return CommunityLicSuspension
     */
    public function setIsActioned($isActioned)
    {
        $this->isActioned = $isActioned;

        return $this;
    }

    /**
     * Get the is actioned
     *
     * @return string
     */
    public function getIsActioned()
    {
        return $this->isActioned;
    }
}
