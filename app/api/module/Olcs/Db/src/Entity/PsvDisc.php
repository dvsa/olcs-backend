<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PsvDisc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="psv_disc",
 *    indexes={
 *        @ORM\Index(name="IDX_5021B8A5FC21D85", columns={"removal_explanation"}),
 *        @ORM\Index(name="IDX_5021B8A5D45B0D47", columns={"removal_reason"}),
 *        @ORM\Index(name="IDX_5021B8A565CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_5021B8A5DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_5021B8A526EF07C9", columns={"licence_id"})
 *    }
 * )
 */
class PsvDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\RemovalExplanationManyToOne,
        Traits\CreatedByManyToOne,
        Traits\RemovalReasonManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\DiscNo50Field,
        Traits\IssuedDateField,
        Traits\CeasedDateField,
        Traits\StartDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is copy
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_copy", nullable=true)
     */
    protected $isCopy;

    /**
     * Reprint required
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="reprint_required", nullable=true)
     */
    protected $reprintRequired;

    /**
     * Set the is copy
     *
     * @param string $isCopy
     * @return PsvDisc
     */
    public function setIsCopy($isCopy)
    {
        $this->isCopy = $isCopy;

        return $this;
    }

    /**
     * Get the is copy
     *
     * @return string
     */
    public function getIsCopy()
    {
        return $this->isCopy;
    }

    /**
     * Set the reprint required
     *
     * @param string $reprintRequired
     * @return PsvDisc
     */
    public function setReprintRequired($reprintRequired)
    {
        $this->reprintRequired = $reprintRequired;

        return $this;
    }

    /**
     * Get the reprint required
     *
     * @return string
     */
    public function getReprintRequired()
    {
        return $this->reprintRequired;
    }
}
