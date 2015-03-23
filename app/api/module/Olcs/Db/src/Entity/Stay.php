<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Stay Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="stay",
 *    indexes={
 *        @ORM\Index(name="ix_stay_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_stay_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_stay_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_stay_outcome", columns={"outcome"}),
 *        @ORM\Index(name="ix_stay_stay_type", columns={"stay_type"})
 *    }
 * )
 */
class Stay implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OutcomeManyToOne,
        Traits\CustomVersionField,
        Traits\WithdrawnDateField;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="stays")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="decision_date", nullable=true)
     */
    protected $decisionDate;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=1024, nullable=true)
     */
    protected $notes;

    /**
     * Request date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="request_date", nullable=true)
     */
    protected $requestDate;

    /**
     * Stay type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="stay_type", referencedColumnName="id", nullable=false)
     */
    protected $stayType;

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return Stay
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the decision date
     *
     * @param \DateTime $decisionDate
     * @return Stay
     */
    public function setDecisionDate($decisionDate)
    {
        $this->decisionDate = $decisionDate;

        return $this;
    }

    /**
     * Get the decision date
     *
     * @return \DateTime
     */
    public function getDecisionDate()
    {
        return $this->decisionDate;
    }

    /**
     * Set the notes
     *
     * @param string $notes
     * @return Stay
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set the request date
     *
     * @param \DateTime $requestDate
     * @return Stay
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * Get the request date
     *
     * @return \DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set the stay type
     *
     * @param \Olcs\Db\Entity\RefData $stayType
     * @return Stay
     */
    public function setStayType($stayType)
    {
        $this->stayType = $stayType;

        return $this;
    }

    /**
     * Get the stay type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getStayType()
    {
        return $this->stayType;
    }
}
