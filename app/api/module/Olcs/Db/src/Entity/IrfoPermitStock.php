<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoPermitStock Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_permit_stock",
 *    indexes={
 *        @ORM\Index(name="ix_irfo_permit_stock_irfo_gv_permit_id", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="ix_irfo_permit_stock_irfo_country_id", columns={"irfo_country_id"}),
 *        @ORM\Index(name="ix_irfo_permit_stock_status", columns={"status"}),
 *        @ORM\Index(name="ix_irfo_permit_stock_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_permit_stock_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_permit_stock_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class IrfoPermitStock implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\IrfoGvPermitManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\StatusManyToOne,
        Traits\CustomVersionField;

    /**
     * Irfo country
     *
     * @var \Olcs\Db\Entity\IrfoCountry
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoCountry")
     * @ORM\JoinColumn(name="irfo_country_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoCountry;

    /**
     * Serial no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_no", nullable=false)
     */
    protected $serialNo;

    /**
     * Valid for year
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="valid_for_year", nullable=false)
     */
    protected $validForYear;

    /**
     * Void return date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="void_return_date", nullable=true)
     */
    protected $voidReturnDate;

    /**
     * Set the irfo country
     *
     * @param \Olcs\Db\Entity\IrfoCountry $irfoCountry
     * @return IrfoPermitStock
     */
    public function setIrfoCountry($irfoCountry)
    {
        $this->irfoCountry = $irfoCountry;

        return $this;
    }

    /**
     * Get the irfo country
     *
     * @return \Olcs\Db\Entity\IrfoCountry
     */
    public function getIrfoCountry()
    {
        return $this->irfoCountry;
    }

    /**
     * Set the serial no
     *
     * @param int $serialNo
     * @return IrfoPermitStock
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;

        return $this;
    }

    /**
     * Get the serial no
     *
     * @return int
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * Set the valid for year
     *
     * @param int $validForYear
     * @return IrfoPermitStock
     */
    public function setValidForYear($validForYear)
    {
        $this->validForYear = $validForYear;

        return $this;
    }

    /**
     * Get the valid for year
     *
     * @return int
     */
    public function getValidForYear()
    {
        return $this->validForYear;
    }

    /**
     * Set the void return date
     *
     * @param \DateTime $voidReturnDate
     * @return IrfoPermitStock
     */
    public function setVoidReturnDate($voidReturnDate)
    {
        $this->voidReturnDate = $voidReturnDate;

        return $this;
    }

    /**
     * Get the void return date
     *
     * @return \DateTime
     */
    public function getVoidReturnDate()
    {
        return $this->voidReturnDate;
    }
}
