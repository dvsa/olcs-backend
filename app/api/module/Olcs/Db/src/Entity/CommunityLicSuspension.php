<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CommunityLicSuspension Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="community_lic_suspension",
 *    indexes={
 *        @ORM\Index(name="fk_community_lic_suspension_community_lic1_idx", columns={"community_lic_id"}),
 *        @ORM\Index(name="fk_community_lic_suspension_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_suspension_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicSuspension implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CommunityLicManyToOne,
        Traits\StartDateField,
        Traits\EndDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is actioned
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_actioned", nullable=true)
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
