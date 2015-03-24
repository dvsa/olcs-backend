<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Disqualification Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="disqualification",
 *    indexes={
 *        @ORM\Index(name="ix_disqualification_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_disqualification_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_disqualification_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_disqualification_officer_cd_id", columns={"officer_cd_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_disqualification_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Disqualification implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Notes4000Field,
        Traits\OlbsKeyField,
        Traits\OrganisationManyToOneAlt1,
        Traits\StartDateFieldAlt1,
        Traits\CustomVersionField;

    /**
     * Is disqualified
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_disqualified", nullable=true)
     */
    protected $isDisqualified;

    /**
     * Officer cd
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="officer_cd_id", referencedColumnName="id", nullable=true)
     */
    protected $officerCd;

    /**
     * Period
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="period", nullable=false)
     */
    protected $period;

    /**
     * Set the is disqualified
     *
     * @param string $isDisqualified
     * @return Disqualification
     */
    public function setIsDisqualified($isDisqualified)
    {
        $this->isDisqualified = $isDisqualified;

        return $this;
    }

    /**
     * Get the is disqualified
     *
     * @return string
     */
    public function getIsDisqualified()
    {
        return $this->isDisqualified;
    }

    /**
     * Set the officer cd
     *
     * @param \Olcs\Db\Entity\ContactDetails $officerCd
     * @return Disqualification
     */
    public function setOfficerCd($officerCd)
    {
        $this->officerCd = $officerCd;

        return $this;
    }

    /**
     * Get the officer cd
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getOfficerCd()
    {
        return $this->officerCd;
    }

    /**
     * Set the period
     *
     * @param int $period
     * @return Disqualification
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get the period
     *
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }
}
