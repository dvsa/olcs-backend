<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmQualification Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_qualification",
 *    indexes={
 *        @ORM\Index(name="IDX_90254A03B59E284E", columns={"qualification_type"}),
 *        @ORM\Index(name="IDX_90254A03F026BB7C", columns={"country_code"}),
 *        @ORM\Index(name="IDX_90254A0365CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_90254A03DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_90254A031F75BD29", columns={"transport_manager_id"})
 *    }
 * )
 */
class TmQualification implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\TransportManagerManyToOneAlt1,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Country code
     *
     * @var \Olcs\Db\Entity\Country
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Country", fetch="LAZY")
     * @ORM\JoinColumn(name="country_code", referencedColumnName="id", nullable=false)
     */
    protected $countryCode;

    /**
     * Qualification type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="qualification_type", referencedColumnName="id", nullable=false)
     */
    protected $qualificationType;

    /**
     * Issued date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="issued_date", nullable=true)
     */
    protected $issuedDate;

    /**
     * Serial no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="serial_no", length=20, nullable=true)
     */
    protected $serialNo;

    /**
     * Set the country code
     *
     * @param \Olcs\Db\Entity\Country $countryCode
     * @return TmQualification
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get the country code
     *
     * @return \Olcs\Db\Entity\Country
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set the qualification type
     *
     * @param \Olcs\Db\Entity\RefData $qualificationType
     * @return TmQualification
     */
    public function setQualificationType($qualificationType)
    {
        $this->qualificationType = $qualificationType;

        return $this;
    }

    /**
     * Get the qualification type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getQualificationType()
    {
        return $this->qualificationType;
    }

    /**
     * Set the issued date
     *
     * @param \DateTime $issuedDate
     * @return TmQualification
     */
    public function setIssuedDate($issuedDate)
    {
        $this->issuedDate = $issuedDate;

        return $this;
    }

    /**
     * Get the issued date
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * Set the serial no
     *
     * @param string $serialNo
     * @return TmQualification
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;

        return $this;
    }

    /**
     * Get the serial no
     *
     * @return string
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }
}
