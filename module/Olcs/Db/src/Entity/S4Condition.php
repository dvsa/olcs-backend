<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * S4Condition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="s4_condition",
 *    indexes={
 *        @ORM\Index(name="IDX_A372ECDD682540E7", columns={"target_condition_id"}),
 *        @ORM\Index(name="IDX_A372ECDD5E9D4F0E", columns={"source_condition_id"}),
 *        @ORM\Index(name="IDX_A372ECDD9E191ED6", columns={"s4_id"})
 *    }
 * )
 */
class S4Condition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\S4ManyToOne;

    /**
     * Target condition
     *
     * @var \Olcs\Db\Entity\ConditionUndertaking
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ConditionUndertaking", fetch="LAZY")
     * @ORM\JoinColumn(name="target_condition_id", referencedColumnName="id", nullable=false)
     */
    protected $targetCondition;

    /**
     * Source condition
     *
     * @var \Olcs\Db\Entity\ConditionUndertaking
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ConditionUndertaking", fetch="LAZY")
     * @ORM\JoinColumn(name="source_condition_id", referencedColumnName="id", nullable=false)
     */
    protected $sourceCondition;

    /**
     * Set the target condition
     *
     * @param \Olcs\Db\Entity\ConditionUndertaking $targetCondition
     * @return S4Condition
     */
    public function setTargetCondition($targetCondition)
    {
        $this->targetCondition = $targetCondition;

        return $this;
    }

    /**
     * Get the target condition
     *
     * @return \Olcs\Db\Entity\ConditionUndertaking
     */
    public function getTargetCondition()
    {
        return $this->targetCondition;
    }

    /**
     * Set the source condition
     *
     * @param \Olcs\Db\Entity\ConditionUndertaking $sourceCondition
     * @return S4Condition
     */
    public function setSourceCondition($sourceCondition)
    {
        $this->sourceCondition = $sourceCondition;

        return $this;
    }

    /**
     * Get the source condition
     *
     * @return \Olcs\Db\Entity\ConditionUndertaking
     */
    public function getSourceCondition()
    {
        return $this->sourceCondition;
    }
}
