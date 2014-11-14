<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * S4Condition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="s4_condition",
 *    indexes={
 *        @ORM\Index(name="fk_s4_condition_Condition1_idx", 
 *            columns={"source_condition_id"}),
 *        @ORM\Index(name="fk_s4_condition_Condition2_idx", 
 *            columns={"target_condition_id"}),
 *        @ORM\Index(name="fk_s4_condition_s41_idx", 
 *            columns={"s4_id"})
 *    }
 * )
 */
class S4Condition implements Interfaces\EntityInterface
{

    /**
     * S4
     *
     * @var \Olcs\Db\Entity\S4
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\S4", fetch="LAZY")
     * @ORM\JoinColumn(name="s4_id", referencedColumnName="id", nullable=false)
     */
    protected $s4;

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
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Set the s4
     *
     * @param \Olcs\Db\Entity\S4 $s4
     * @return S4Condition
     */
    public function setS4($s4)
    {
        $this->s4 = $s4;

        return $this;
    }

    /**
     * Get the s4
     *
     * @return \Olcs\Db\Entity\S4
     */
    public function getS4()
    {
        return $this->s4;
    }

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

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
