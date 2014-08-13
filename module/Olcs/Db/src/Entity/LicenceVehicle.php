<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LicenceVehicle Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="licence_vehicle",
 *    indexes={
 *        @ORM\Index(name="fk_licence_vehicle_vehicle1_idx", columns={"vehicle_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_ref_data1_idx", columns={"removal_reason"}),
 *        @ORM\Index(name="fk_licence_vehicle_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_licence_vehicle_licence1", columns={"licence_id"})
 *    }
 * )
 */
class LicenceVehicle implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\RemovalReasonManyToOne,
        Traits\ApplicationManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomDeletedDateField,
        Traits\ViAction1Field,
        Traits\SpecifiedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Vehicle
     *
     * @var \Olcs\Db\Entity\Vehicle
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Vehicle", fetch="LAZY", inversedBy="licenceVehicles")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    protected $vehicle;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY", inversedBy="licenceVehicles")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id")
     */
    protected $licence;

    /**
     * Application received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="application_received_date", nullable=true)
     */
    protected $applicationReceivedDate;

    /**
     * Removal
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="removal", nullable=true)
     */
    protected $removal;

    /**
     * Removal letter seed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="removal_letter_seed_date", nullable=true)
     */
    protected $removalLetterSeedDate;

    /**
     * Is interim
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="is_interim", nullable=true)
     */
    protected $isInterim;

    /**
     * Warning letter seed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="warning_letter_seed_date", nullable=true)
     */
    protected $warningLetterSeedDate;

    /**
     * Warning letter sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="warning_letter_sent_date", nullable=true)
     */
    protected $warningLetterSentDate;


    /**
     * Set the vehicle
     *
     * @param \Olcs\Db\Entity\Vehicle $vehicle
     * @return LicenceVehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * Get the vehicle
     *
     * @return \Olcs\Db\Entity\Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return LicenceVehicle
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
     * Set the application received date
     *
     * @param \DateTime $applicationReceivedDate
     * @return LicenceVehicle
     */
    public function setApplicationReceivedDate($applicationReceivedDate)
    {
        $this->applicationReceivedDate = $applicationReceivedDate;

        return $this;
    }

    /**
     * Get the application received date
     *
     * @return \DateTime
     */
    public function getApplicationReceivedDate()
    {
        return $this->applicationReceivedDate;
    }

    /**
     * Set the removal
     *
     * @param string $removal
     * @return LicenceVehicle
     */
    public function setRemoval($removal)
    {
        $this->removal = $removal;

        return $this;
    }

    /**
     * Get the removal
     *
     * @return string
     */
    public function getRemoval()
    {
        return $this->removal;
    }

    /**
     * Set the removal letter seed date
     *
     * @param \DateTime $removalLetterSeedDate
     * @return LicenceVehicle
     */
    public function setRemovalLetterSeedDate($removalLetterSeedDate)
    {
        $this->removalLetterSeedDate = $removalLetterSeedDate;

        return $this;
    }

    /**
     * Get the removal letter seed date
     *
     * @return \DateTime
     */
    public function getRemovalLetterSeedDate()
    {
        return $this->removalLetterSeedDate;
    }

    /**
     * Set the is interim
     *
     * @param int $isInterim
     * @return LicenceVehicle
     */
    public function setIsInterim($isInterim)
    {
        $this->isInterim = $isInterim;

        return $this;
    }

    /**
     * Get the is interim
     *
     * @return int
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }

    /**
     * Set the warning letter seed date
     *
     * @param \DateTime $warningLetterSeedDate
     * @return LicenceVehicle
     */
    public function setWarningLetterSeedDate($warningLetterSeedDate)
    {
        $this->warningLetterSeedDate = $warningLetterSeedDate;

        return $this;
    }

    /**
     * Get the warning letter seed date
     *
     * @return \DateTime
     */
    public function getWarningLetterSeedDate()
    {
        return $this->warningLetterSeedDate;
    }

    /**
     * Set the warning letter sent date
     *
     * @param \DateTime $warningLetterSentDate
     * @return LicenceVehicle
     */
    public function setWarningLetterSentDate($warningLetterSentDate)
    {
        $this->warningLetterSentDate = $warningLetterSentDate;

        return $this;
    }

    /**
     * Get the warning letter sent date
     *
     * @return \DateTime
     */
    public function getWarningLetterSentDate()
    {
        return $this->warningLetterSentDate;
    }
}
