<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Reason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="reason",
 *    indexes={
 *        @ORM\Index(name="fk_pi_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_reason_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_reason_ref_data1_idx", columns={"goods_or_psv"})
 *    }
 * )
 */
class Reason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\GoodsOrPsvManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\SectionCode50Field,
        Traits\Description255Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Submission action
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\SubmissionAction", mappedBy="reasons", fetch="LAZY")
     */
    protected $submissionActions;

    /**
     * Pi
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Pi", mappedBy="reasons", fetch="LAZY")
     */
    protected $pis;

    /**
     * Propose to revoke
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\ProposeToRevoke", mappedBy="reasons", fetch="LAZY")
     */
    protected $proposeToRevokes;

    /**
     * Is read only
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_read_only", nullable=false)
     */
    protected $isReadOnly;

    /**
     * Is ni
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_ni", nullable=false)
     */
    protected $isNi;

    /**
     * Is propose to revoke
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_propose_to_revoke", nullable=false)
     */
    protected $isProposeToRevoke;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->submissionActions = new ArrayCollection();
        $this->pis = new ArrayCollection();
        $this->proposeToRevokes = new ArrayCollection();
    }

    /**
     * Set the submission action
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionActions
     * @return Reason
     */
    public function setSubmissionActions($submissionActions)
    {
        $this->submissionActions = $submissionActions;

        return $this;
    }

    /**
     * Get the submission actions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubmissionActions()
    {
        return $this->submissionActions;
    }

    /**
     * Add a submission actions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionActions
     * @return Reason
     */
    public function addSubmissionActions($submissionActions)
    {
        if ($submissionActions instanceof ArrayCollection) {
            $this->submissionActions = new ArrayCollection(
                array_merge(
                    $this->submissionActions->toArray(),
                    $submissionActions->toArray()
                )
            );
        } elseif (!$this->submissionActions->contains($submissionActions)) {
            $this->submissionActions->add($submissionActions);
        }

        return $this;
    }

    /**
     * Remove a submission actions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionActions
     * @return Reason
     */
    public function removeSubmissionActions($submissionActions)
    {
        if ($this->submissionActions->contains($submissionActions)) {
            $this->submissionActions->removeElement($submissionActions);
        }

        return $this;
    }

    /**
     * Set the pi
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return Reason
     */
    public function setPis($pis)
    {
        $this->pis = $pis;

        return $this;
    }

    /**
     * Get the pis
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPis()
    {
        return $this->pis;
    }

    /**
     * Add a pis
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return Reason
     */
    public function addPis($pis)
    {
        if ($pis instanceof ArrayCollection) {
            $this->pis = new ArrayCollection(
                array_merge(
                    $this->pis->toArray(),
                    $pis->toArray()
                )
            );
        } elseif (!$this->pis->contains($pis)) {
            $this->pis->add($pis);
        }

        return $this;
    }

    /**
     * Remove a pis
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return Reason
     */
    public function removePis($pis)
    {
        if ($this->pis->contains($pis)) {
            $this->pis->removeElement($pis);
        }

        return $this;
    }

    /**
     * Set the propose to revoke
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $proposeToRevokes
     * @return Reason
     */
    public function setProposeToRevokes($proposeToRevokes)
    {
        $this->proposeToRevokes = $proposeToRevokes;

        return $this;
    }

    /**
     * Get the propose to revokes
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProposeToRevokes()
    {
        return $this->proposeToRevokes;
    }

    /**
     * Add a propose to revokes
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $proposeToRevokes
     * @return Reason
     */
    public function addProposeToRevokes($proposeToRevokes)
    {
        if ($proposeToRevokes instanceof ArrayCollection) {
            $this->proposeToRevokes = new ArrayCollection(
                array_merge(
                    $this->proposeToRevokes->toArray(),
                    $proposeToRevokes->toArray()
                )
            );
        } elseif (!$this->proposeToRevokes->contains($proposeToRevokes)) {
            $this->proposeToRevokes->add($proposeToRevokes);
        }

        return $this;
    }

    /**
     * Remove a propose to revokes
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $proposeToRevokes
     * @return Reason
     */
    public function removeProposeToRevokes($proposeToRevokes)
    {
        if ($this->proposeToRevokes->contains($proposeToRevokes)) {
            $this->proposeToRevokes->removeElement($proposeToRevokes);
        }

        return $this;
    }

    /**
     * Set the is read only
     *
     * @param string $isReadOnly
     * @return Reason
     */
    public function setIsReadOnly($isReadOnly)
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * Get the is read only
     *
     * @return string
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * Set the is ni
     *
     * @param string $isNi
     * @return Reason
     */
    public function setIsNi($isNi)
    {
        $this->isNi = $isNi;

        return $this;
    }

    /**
     * Get the is ni
     *
     * @return string
     */
    public function getIsNi()
    {
        return $this->isNi;
    }

    /**
     * Set the is propose to revoke
     *
     * @param string $isProposeToRevoke
     * @return Reason
     */
    public function setIsProposeToRevoke($isProposeToRevoke)
    {
        $this->isProposeToRevoke = $isProposeToRevoke;

        return $this;
    }

    /**
     * Get the is propose to revoke
     *
     * @return string
     */
    public function getIsProposeToRevoke()
    {
        return $this->isProposeToRevoke;
    }
}
