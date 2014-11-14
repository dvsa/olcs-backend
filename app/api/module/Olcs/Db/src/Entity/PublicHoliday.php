<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicHoliday Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="public_holiday",
 *    indexes={
 *        @ORM\Index(name="fk_public_holiday_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_public_holiday_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class PublicHoliday implements Interfaces\EntityInterface
{

    /**
     * Public holiday date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="public_holiday_date", nullable=false)
     */
    protected $publicHolidayDate;

    /**
     * Is england
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_england", nullable=true)
     */
    protected $isEngland;

    /**
     * Is wales
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_wales", nullable=true)
     */
    protected $isWales;

    /**
     * Is scotland
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_scotland", nullable=true)
     */
    protected $isScotland;

    /**
     * Is ni
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_ni", nullable=true)
     */
    protected $isNi;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

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
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
