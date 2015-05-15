<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * CompaniesHouseAlert Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_alert",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_alert_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_companies_house_alert_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_companies_house_alert_organisation_id", columns={"organisation_id"})
 *    }
 * )
 */
class CompaniesHouseAlert implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CompanyOrLlpNo20Field,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\CustomLastModifiedOnField,
        Traits\Name160Field,
        Traits\OrganisationManyToOne,
        Traits\CustomVersionField;

    /**
     * Created by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="created_by", nullable=true)
     */
    protected $createdBy;

    /**
     * Last modified by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="last_modified_by", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Reason
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\CompaniesHouseAlertReason", mappedBy="companiesHouseAlert", cascade={"persist"})
     */
    protected $reasons;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->reasons = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param int $createdBy
     * @return CompaniesHouseAlert
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the last modified by
     *
     * @param int $lastModifiedBy
     * @return CompaniesHouseAlert
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return int
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the reason
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return CompaniesHouseAlert
     */
    public function setReasons($reasons)
    {
        $this->reasons = $reasons;

        return $this;
    }

    /**
     * Get the reasons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getReasons()
    {
        return $this->reasons;
    }

    /**
     * Add a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return CompaniesHouseAlert
     */
    public function addReasons($reasons)
    {
        if ($reasons instanceof ArrayCollection) {
            $this->reasons = new ArrayCollection(
                array_merge(
                    $this->reasons->toArray(),
                    $reasons->toArray()
                )
            );
        } elseif (!$this->reasons->contains($reasons)) {
            $this->reasons->add($reasons);
        }

        return $this;
    }

    /**
     * Remove a reasons
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $reasons
     * @return CompaniesHouseAlert
     */
    public function removeReasons($reasons)
    {
        if ($this->reasons->contains($reasons)) {
            $this->reasons->removeElement($reasons);
        }

        return $this;
    }
}
