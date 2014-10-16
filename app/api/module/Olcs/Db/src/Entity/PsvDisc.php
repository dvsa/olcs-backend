<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PsvDisc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="psv_disc",
 *    indexes={
 *        @ORM\Index(name="fk_psv_disc_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_psv_disc_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_psv_disc_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_psv_disc_ref_data1_idx", columns={"removal_reason"}),
 *        @ORM\Index(name="fk_psv_disc_ref_data2_idx", columns={"removal_explanation"})
 *    }
 * )
 */
class PsvDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\RemovalExplanationManyToOne,
        Traits\RemovalReasonManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\DiscNo50Field,
        Traits\IssuedDateField,
        Traits\CeasedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY", inversedBy="psvDiscs")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Is copy
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_copy", nullable=true)
     */
    protected $isCopy;

    /**
     * Reprint required
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="reprint_required", nullable=true)
     */
    protected $reprintRequired;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date", nullable=true)
     */
    protected $startDate;

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return PsvDisc
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
     * Set the is copy
     *
     * @param string $isCopy
     * @return PsvDisc
     */
    public function setIsCopy($isCopy)
    {
        $this->isCopy = $isCopy;

        return $this;
    }

    /**
     * Get the is copy
     *
     * @return string
     */
    public function getIsCopy()
    {
        return $this->isCopy;
    }

    /**
     * Set the reprint required
     *
     * @param string $reprintRequired
     * @return PsvDisc
     */
    public function setReprintRequired($reprintRequired)
    {
        $this->reprintRequired = $reprintRequired;

        return $this;
    }

    /**
     * Get the reprint required
     *
     * @return string
     */
    public function getReprintRequired()
    {
        return $this->reprintRequired;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return PsvDisc
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
