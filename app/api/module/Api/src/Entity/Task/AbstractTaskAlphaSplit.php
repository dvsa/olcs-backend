<?php

namespace Dvsa\Olcs\Api\Entity\Task;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskAlphaSplit Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="task_alpha_split",
 *    indexes={
 *        @ORM\Index(name="ix_task_alpha_split_task_allocation_rules_id", columns={"task_allocation_rules_id"}),
 *        @ORM\Index(name="ix_task_alpha_split_user_id", columns={"user_id"})
 *    }
 * )
 */
abstract class AbstractTaskAlphaSplit
{

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
     * Split from inclusive
     *
     * @var string
     *
     * @ORM\Column(type="string", name="split_from_inclusive", length=2, nullable=false)
     */
    protected $splitFromInclusive;

    /**
     * Split to inclusive
     *
     * @var string
     *
     * @ORM\Column(type="string", name="split_to_inclusive", length=2, nullable=false)
     */
    protected $splitToInclusive;

    /**
     * Task allocation rules
     *
     * @var \Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule")
     * @ORM\JoinColumn(name="task_allocation_rules_id", referencedColumnName="id", nullable=false)
     */
    protected $taskAllocationRules;

    /**
     * User
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Set the id
     *
     * @param int $id
     * @return TaskAlphaSplit
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

    /**
     * Set the split from inclusive
     *
     * @param string $splitFromInclusive
     * @return TaskAlphaSplit
     */
    public function setSplitFromInclusive($splitFromInclusive)
    {
        $this->splitFromInclusive = $splitFromInclusive;

        return $this;
    }

    /**
     * Get the split from inclusive
     *
     * @return string
     */
    public function getSplitFromInclusive()
    {
        return $this->splitFromInclusive;
    }

    /**
     * Set the split to inclusive
     *
     * @param string $splitToInclusive
     * @return TaskAlphaSplit
     */
    public function setSplitToInclusive($splitToInclusive)
    {
        $this->splitToInclusive = $splitToInclusive;

        return $this;
    }

    /**
     * Get the split to inclusive
     *
     * @return string
     */
    public function getSplitToInclusive()
    {
        return $this->splitToInclusive;
    }

    /**
     * Set the task allocation rules
     *
     * @param \Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule $taskAllocationRules
     * @return TaskAlphaSplit
     */
    public function setTaskAllocationRules($taskAllocationRules)
    {
        $this->taskAllocationRules = $taskAllocationRules;

        return $this;
    }

    /**
     * Get the task allocation rules
     *
     * @return \Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule
     */
    public function getTaskAllocationRules()
    {
        return $this->taskAllocationRules;
    }

    /**
     * Set the user
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $user
     * @return TaskAlphaSplit
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
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
}
