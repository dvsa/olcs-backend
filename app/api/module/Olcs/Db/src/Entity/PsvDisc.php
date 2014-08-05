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
 *        @ORM\Index(name="fk_psv_disc_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_psv_disc_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_psv_disc_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_psv_disc_ref_data1_idx", columns={"removal_reason"}),
 *        @ORM\Index(name="fk_psv_disc_ref_data2_idx", columns={"removal_explanation"})
 *    }
 * )
 */
class PsvDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\RemovalExplanationManyToOne,
        Traits\RemovalReasonManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\DiscNo50Field,
        Traits\IssuedDateFieldAlt1,
        Traits\CeasedDateField,
        Traits\StartDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is copy
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_copy", nullable=true)
     */
    protected $isCopy;

    /**
     * Reprint required
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="reprint_required", nullable=true)
     */
    protected $reprintRequired;

    /**
     * Set the is copy
     *
     * @param boolean $isCopy
     * @return \Olcs\Db\Entity\PsvDisc
     */
    public function setIsCopy($isCopy)
    {
        $this->isCopy = $isCopy;

        return $this;
    }

    /**
     * Get the is copy
     *
     * @return boolean
     */
    public function getIsCopy()
    {
        return $this->isCopy;
    }

    /**
     * Set the reprint required
     *
     * @param boolean $reprintRequired
     * @return \Olcs\Db\Entity\PsvDisc
     */
    public function setReprintRequired($reprintRequired)
    {
        $this->reprintRequired = $reprintRequired;

        return $this;
    }

    /**
     * Get the reprint required
     *
     * @return boolean
     */
    public function getReprintRequired()
    {
        return $this->reprintRequired;
    }
}
