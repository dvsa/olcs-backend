<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * GracePeriod Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="grace_period",
 *    indexes={
 *        @ORM\Index(name="ix_grace_period_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_grace_period_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_grace_period_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_grace_period_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class GracePeriod implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=90, nullable=false)
     */
    protected $description;

    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date", nullable=false)
     */
    protected $endDate;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date", nullable=false)
     */
    protected $startDate;

    /**
     * Set the description
     *
     * @param string $description
     * @return GracePeriod
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

    /**
     * Set the end date
     *
     * @param \DateTime $endDate
     * @return GracePeriod
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return GracePeriod
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the start date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
}
