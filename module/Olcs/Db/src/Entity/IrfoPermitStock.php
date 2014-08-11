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
 *        @ORM\Index(name="fk_irfo_permit_stock_irfo_gv_permit1_idx", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="fk_irfo_permit_stock_irfo_country1_idx", columns={"irfo_country_id"}),
 *        @ORM\Index(name="fk_irfo_permit_stock_ref_data1_idx", columns={"status"}),
 *        @ORM\Index(name="fk_irfo_permit_stock_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_permit_stock_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoPermitStock implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\StatusManyToOne,
        Traits\IrfoCountryManyToOne,
        Traits\IrfoGvPermitManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * @ORM\Column(type="integer", name="valid_for_year", nullable=false)
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
