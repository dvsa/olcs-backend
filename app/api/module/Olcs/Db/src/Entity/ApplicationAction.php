<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ApplicationAction Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_action",
 *    indexes={
 *        @ORM\Index(name="fk_application_action_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_application_action_application_action_ref1_idx", columns={"application_action_ref_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="application_id", columns={"application_id","application_action_ref_id"})
 *    }
 * )
 */
class ApplicationAction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ApplicationManyToOne;

    /**
     * Application action ref
     *
     * @var \Olcs\Db\Entity\ApplicationActionRef
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ApplicationActionRef")
     * @ORM\JoinColumn(name="application_action_ref_id", referencedColumnName="id")
     */
    protected $applicationActionRef;

    /**
     * Is received
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="is_received", nullable=true)
     */
    protected $isReceived;

    /**
     * Is approved
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="is_approved", nullable=true)
     */
    protected $isApproved;

    /**
     * Is applicable
     *
     * @var unknown
     *
     * @ORM\Column(type="yesnonull", name="is_applicable", nullable=true)
     */
    protected $isApplicable;


    /**
     * Set the application action ref
     *
     * @param \Olcs\Db\Entity\ApplicationActionRef $applicationActionRef
     * @return ApplicationAction
     */
    public function setApplicationActionRef($applicationActionRef)
    {
        $this->applicationActionRef = $applicationActionRef;

        return $this;
    }

    /**
     * Get the application action ref
     *
     * @return \Olcs\Db\Entity\ApplicationActionRef
     */
    public function getApplicationActionRef()
    {
        return $this->applicationActionRef;
    }


    /**
     * Set the is received
     *
     * @param unknown $isReceived
     * @return ApplicationAction
     */
    public function setIsReceived($isReceived)
    {
        $this->isReceived = $isReceived;

        return $this;
    }

    /**
     * Get the is received
     *
     * @return unknown
     */
    public function getIsReceived()
    {
        return $this->isReceived;
    }


    /**
     * Set the is approved
     *
     * @param unknown $isApproved
     * @return ApplicationAction
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get the is approved
     *
     * @return unknown
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }


    /**
     * Set the is applicable
     *
     * @param unknown $isApplicable
     * @return ApplicationAction
     */
    public function setIsApplicable($isApplicable)
    {
        $this->isApplicable = $isApplicable;

        return $this;
    }

    /**
     * Get the is applicable
     *
     * @return unknown
     */
    public function getIsApplicable()
    {
        return $this->isApplicable;
    }

}
