<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PublicHoliday Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="public_holiday",
 *    indexes={
 *        @ORM\Index(name="fk_public_holiday_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_public_holiday_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PublicHoliday implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is england
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_england", nullable=true)
     */
    protected $isEngland;

    /**
     * Is ni
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_ni", nullable=true)
     */
    protected $isNi;

    /**
     * Is scotland
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_scotland", nullable=true)
     */
    protected $isScotland;

    /**
     * Is wales
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_wales", nullable=true)
     */
    protected $isWales;

    /**
     * Public holiday date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="public_holiday_date", nullable=false)
     */
    protected $publicHolidayDate;

    /**
     * Set the is england
     *
     * @param string $isEngland
     * @return PublicHoliday
     */
    public function setIsEngland($isEngland)
    {
        $this->isEngland = $isEngland;

        return $this;
    }

    /**
     * Get the is england
     *
     * @return string
     */
    public function getIsEngland()
    {
        return $this->isEngland;
    }

    /**
     * Set the is ni
     *
     * @param string $isNi
     * @return PublicHoliday
     */
    public function setIsNi($isNi)
    {
        $this->isNi = $isNi;

        return $this;
    }

    /**
     * Get the is ni
     *
     * @return string
     */
    public function getIsNi()
    {
        return $this->isNi;
    }

    /**
     * Set the is scotland
     *
     * @param string $isScotland
     * @return PublicHoliday
     */
    public function setIsScotland($isScotland)
    {
        $this->isScotland = $isScotland;

        return $this;
    }

    /**
     * Get the is scotland
     *
     * @return string
     */
    public function getIsScotland()
    {
        return $this->isScotland;
    }

    /**
     * Set the is wales
     *
     * @param string $isWales
     * @return PublicHoliday
     */
    public function setIsWales($isWales)
    {
        $this->isWales = $isWales;

        return $this;
    }

    /**
     * Get the is wales
     *
     * @return string
     */
    public function getIsWales()
    {
        return $this->isWales;
    }

    /**
     * Set the public holiday date
     *
     * @param \DateTime $publicHolidayDate
     * @return PublicHoliday
     */
    public function setPublicHolidayDate($publicHolidayDate)
    {
        $this->publicHolidayDate = $publicHolidayDate;

        return $this;
    }

    /**
     * Get the public holiday date
     *
     * @return \DateTime
     */
    public function getPublicHolidayDate()
    {
        return $this->publicHolidayDate;
    }
}
