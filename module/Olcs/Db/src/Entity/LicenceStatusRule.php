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
 *        @ORM\Index(name="fk_licence_status_rule_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_licence_status_rule_ref_data1_idx", columns={"licence_status"}),
 *        @ORM\Index(name="fk_licence_status_rule_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_status_rule_user2_idx", columns={"last_modified_by"})
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
