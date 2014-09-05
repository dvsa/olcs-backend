<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LegacyPiReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="legacy_pi_reason",
 *    indexes={
 *        @ORM\Index(name="fk_pi_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_reason_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class LegacyPiReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\GoodsOrPsv3Field,
        Traits\SectionCode50Field,
        Traits\Description255Field,
        Traits\IsReadOnlyField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is ni
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_ni", nullable=false)
     */
    protected $isNi;

    /**
     * Is decision
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="is_decision", nullable=false)
     */
    protected $isDecision;

    /**
     * Set the is ni
     *
     * @param boolean $isNi
     * @return LegacyPiReason
     */
    public function setIsNi($isNi)
    {
        $this->isNi = $isNi;

        return $this;
    }

    /**
     * Get the is ni
     *
     * @return boolean
     */
    public function getIsNi()
    {
        return $this->isNi;
    }


    /**
     * Set the is decision
     *
     * @param int $isDecision
     * @return LegacyPiReason
     */
    public function setIsDecision($isDecision)
    {
        $this->isDecision = $isDecision;

        return $this;
    }

    /**
     * Get the is decision
     *
     * @return int
     */
    public function getIsDecision()
    {
        return $this->isDecision;
    }

}
