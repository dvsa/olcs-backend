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
 *        @ORM\Index(name="IDX_1839BF63DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_1839BF639B8FCA82", columns={"community_lic_id"}),
 *        @ORM\Index(name="IDX_1839BF6365CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class CommunityLicSuspension implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CommunityLicManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\StartDateField,
        Traits\EndDateFieldAlt1,
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
    protected $isActioned;

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
