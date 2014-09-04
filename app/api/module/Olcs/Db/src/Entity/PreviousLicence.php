<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PreviousLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="previous_licence",
 *    indexes={
 *        @ORM\Index(name="fk_previous_licence_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_previous_licence_ref_data1_idx", columns={"previous_licence_type"}),
 *        @ORM\Index(name="fk_previous_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_previous_licence_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PreviousLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ApplicationManyToOne,
        Traits\LicNo18Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Previous licence type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="previous_licence_type", referencedColumnName="id", nullable=false)
     */
    protected $previousLicenceType;

    /**
     * Holder name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="holder_name", length=90, nullable=true)
     */
    protected $holderName;

    /**
     * Purchase date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="purchase_date", nullable=true)
     */
    protected $purchaseDate;

    /**
     * Will surrender
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="will_surrender", nullable=true)
     */
    protected $willSurrender;

    /**
     * Disqualification date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="disqualification_date", nullable=true)
     */
    protected $disqualificationDate;

    /**
     * Disqualification length
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="disqualification_length", nullable=true)
     */
    protected $disqualificationLength;

    /**
     * Set the previous licence type
     *
     * @param \Olcs\Db\Entity\RefData $previousLicenceType
     * @return PreviousLicence
     */
    public function setPreviousLicenceType($previousLicenceType)
    {
        $this->previousLicenceType = $previousLicenceType;

        return $this;
    }

    /**
     * Get the previous licence type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPreviousLicenceType()
    {
        return $this->previousLicenceType;
    }


    /**
     * Set the holder name
     *
     * @param string $holderName
     * @return PreviousLicence
     */
    public function setHolderName($holderName)
    {
        $this->holderName = $holderName;

        return $this;
    }

    /**
     * Get the holder name
     *
     * @return string
     */
    public function getHolderName()
    {
        return $this->holderName;
    }


    /**
     * Set the purchase date
     *
     * @param \DateTime $purchaseDate
     * @return PreviousLicence
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get the purchase date
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }


    /**
     * Set the will surrender
     *
     * @param string $willSurrender
     * @return PreviousLicence
     */
    public function setWillSurrender($willSurrender)
    {
        $this->willSurrender = $willSurrender;

        return $this;
    }

    /**
     * Get the will surrender
     *
     * @return string
     */
    public function getWillSurrender()
    {
        return $this->willSurrender;
    }


    /**
     * Set the disqualification date
     *
     * @param \DateTime $disqualificationDate
     * @return PreviousLicence
     */
    public function setDisqualificationDate($disqualificationDate)
    {
        $this->disqualificationDate = $disqualificationDate;

        return $this;
    }

    /**
     * Get the disqualification date
     *
     * @return \DateTime
     */
    public function getDisqualificationDate()
    {
        return $this->disqualificationDate;
    }


    /**
     * Set the disqualification length
     *
     * @param int $disqualificationLength
     * @return PreviousLicence
     */
    public function setDisqualificationLength($disqualificationLength)
    {
        $this->disqualificationLength = $disqualificationLength;

        return $this;
    }

    /**
     * Get the disqualification length
     *
     * @return int
     */
    public function getDisqualificationLength()
    {
        return $this->disqualificationLength;
    }

}
