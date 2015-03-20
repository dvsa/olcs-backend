<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LicenceStatusRule Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="licence_status_rule",
 *    indexes={
 *        @ORM\Index(name="ix_licence_status_rule_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_licence_status_rule_licence_status", columns={"licence_status"}),
 *        @ORM\Index(name="ix_licence_status_rule_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_status_rule_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class LicenceStatusRule implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\EndDateFieldAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * End processed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="end_processed_date", nullable=true)
     */
    protected $endProcessedDate;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="licenceStatusRules")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Licence status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="licence_status", referencedColumnName="id", nullable=false)
     */
    protected $licenceStatus;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="start_date", nullable=false)
     */
    protected $startDate;

    /**
     * Start processed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="start_processed_date", nullable=true)
     */
    protected $startProcessedDate;

    /**
     * Set the end processed date
     *
     * @param \DateTime $endProcessedDate
     * @return LicenceStatusRule
     */
    public function setEndProcessedDate($endProcessedDate)
    {
        $this->endProcessedDate = $endProcessedDate;

        return $this;
    }

    /**
     * Get the end processed date
     *
     * @return \DateTime
     */
    public function getEndProcessedDate()
    {
        return $this->endProcessedDate;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return LicenceStatusRule
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the licence status
     *
     * @param \Olcs\Db\Entity\RefData $licenceStatus
     * @return LicenceStatusRule
     */
    public function setLicenceStatus($licenceStatus)
    {
        $this->licenceStatus = $licenceStatus;

        return $this;
    }

    /**
     * Get the licence status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getLicenceStatus()
    {
        return $this->licenceStatus;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return LicenceStatusRule
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

    /**
     * Set the start processed date
     *
     * @param \DateTime $startProcessedDate
     * @return LicenceStatusRule
     */
    public function setStartProcessedDate($startProcessedDate)
    {
        $this->startProcessedDate = $startProcessedDate;

        return $this;
    }

    /**
     * Get the start processed date
     *
     * @return \DateTime
     */
    public function getStartProcessedDate()
    {
        return $this->startProcessedDate;
    }
}
