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
 *        @ORM\Index(name="IDX_5E09839C1108BFA3", columns={"stay_type"}),
 *        @ORM\Index(name="IDX_5E09839CDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_5E09839CCF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_5E09839C65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_5E09839C30BC6DC2", columns={"outcome"})
 *    }
 * )
 */
class Stay implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\OutcomeManyToOne,
        Traits\WithdrawnDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Stay type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="stay_type", referencedColumnName="id", nullable=false)
     */
    protected $stayType;

    /**
     * Request date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="request_date", nullable=true)
     */
    protected $requestDate;

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
}
