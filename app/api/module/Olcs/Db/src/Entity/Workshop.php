<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Workshop Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="workshop",
 *    indexes={
 *        @ORM\Index(name="ix_workshop_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_workshop_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_workshop_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_workshop_contact_details_id", columns={"contact_details_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_workshop_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Workshop implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ContactDetailsManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\RemovedDateField,
        Traits\CustomVersionField;

    /**
     * Is external
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_external", nullable=false, options={"default": 0})
     */
    protected $isExternal = 0;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="workshops")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Maintenance
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="maintenance", nullable=false, options={"default": 0})
     */
    protected $maintenance = 0;

    /**
     * Safety inspection
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="safety_inspection", nullable=false, options={"default": 0})
     */
    protected $safetyInspection = 0;

    /**
     * Set the is external
     *
     * @param string $isExternal
     * @return Workshop
     */
    public function setIsExternal($isExternal)
    {
        $this->isExternal = $isExternal;

        return $this;
    }

    /**
     * Get the is external
     *
     * @return string
     */
    public function getIsExternal()
    {
        return $this->isExternal;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return Workshop
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
     * Set the maintenance
     *
     * @param string $maintenance
     * @return Workshop
     */
    public function setMaintenance($maintenance)
    {
        $this->maintenance = $maintenance;

        return $this;
    }

    /**
     * Get the maintenance
     *
     * @return string
     */
    public function getMaintenance()
    {
        return $this->maintenance;
    }

    /**
     * Set the safety inspection
     *
     * @param string $safetyInspection
     * @return Workshop
     */
    public function setSafetyInspection($safetyInspection)
    {
        $this->safetyInspection = $safetyInspection;

        return $this;
    }

    /**
     * Get the safety inspection
     *
     * @return string
     */
    public function getSafetyInspection()
    {
        return $this->safetyInspection;
    }
}
