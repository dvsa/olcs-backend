<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * SubmissionSection Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission_section")
 */
class SubmissionSection implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Case
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Cases", mappedBy="submissionSections", fetch="LAZY")
     */
    protected $cases;

    /**
     * Group
     *
     * @var string
     *
     * @ORM\Column(type="string", name="group", length=45, nullable=true)
     */
    protected $group;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=45, nullable=true)
     */
    protected $description;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->cases = new ArrayCollection();
    }

    /**
     * Set the case
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return SubmissionSection
     */
    public function setCases($cases)
    {
        $this->cases = $cases;

        return $this;
    }

    /**
     * Get the cases
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * Add a cases
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return SubmissionSection
     */
    public function addCases($cases)
    {
        if ($cases instanceof ArrayCollection) {
            $this->cases = new ArrayCollection(
                array_merge(
                    $this->cases->toArray(),
                    $cases->toArray()
                )
            );
        } elseif (!$this->cases->contains($cases)) {
            $this->cases->add($cases);
        }

        return $this;
    }

    /**
     * Remove a cases
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return SubmissionSection
     */
    public function removeCases($cases)
    {
        if ($this->cases->contains($cases)) {
            $this->cases->removeElement($cases);
        }

        return $this;
    }

    /**
     * Set the group
     *
     * @param string $group
     * @return SubmissionSection
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get the group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return SubmissionSection
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
