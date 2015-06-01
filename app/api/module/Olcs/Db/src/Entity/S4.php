<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * S4 Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="s4",
 *    indexes={
 *        @ORM\Index(name="ix_s4_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_s4_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_s4_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_s4_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class S4 implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ApplicationManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\ReceivedDateFieldAlt1,
        Traits\CustomVersionField;

    /**
     * Agreed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="agreed_date", nullable=true)
     */
    protected $agreedDate;

    /**
     * Is true s4
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_true_s4", nullable=false, options={"default": 0})
     */
    protected $isTrueS4 = 0;

    /**
     * Outcome
     *
     * @var string
     *
     * @ORM\Column(type="string", name="outcome", length=20, nullable=true)
     */
    protected $outcome;

    /**
     * Surrender licence
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="surrender_licence", nullable=false, options={"default": 0})
     */
    protected $surrenderLicence = 0;

    /**
     * Set the agreed date
     *
     * @param \DateTime $agreedDate
     * @return S4
     */
    public function setAgreedDate($agreedDate)
    {
        $this->agreedDate = $agreedDate;

        return $this;
    }

    /**
     * Get the agreed date
     *
     * @return \DateTime
     */
    public function getAgreedDate()
    {
        return $this->agreedDate;
    }

    /**
     * Set the is true s4
     *
     * @param string $isTrueS4
     * @return S4
     */
    public function setIsTrueS4($isTrueS4)
    {
        $this->isTrueS4 = $isTrueS4;

        return $this;
    }

    /**
     * Get the is true s4
     *
     * @return string
     */
    public function getIsTrueS4()
    {
        return $this->isTrueS4;
    }

    /**
     * Set the outcome
     *
     * @param string $outcome
     * @return S4
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get the outcome
     *
     * @return string
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * Set the surrender licence
     *
     * @param string $surrenderLicence
     * @return S4
     */
    public function setSurrenderLicence($surrenderLicence)
    {
        $this->surrenderLicence = $surrenderLicence;

        return $this;
    }

    /**
     * Get the surrender licence
     *
     * @return string
     */
    public function getSurrenderLicence()
    {
        return $this->surrenderLicence;
    }
}
