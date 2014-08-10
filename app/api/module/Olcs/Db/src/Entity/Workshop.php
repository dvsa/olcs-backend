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
 *        @ORM\Index(name="fk_workshop_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_workshop_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_workshop_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_workshop_contact_details1_idx", columns={"contact_details_id"})
 *    }
 * )
 */
class Workshop implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ContactDetailsManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\RemovedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is external
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_external", nullable=false)
     */
    protected $isExternal;

    /**
     * Maintenance
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="maintenance", nullable=false)
     */
    protected $maintenance;

    /**
     * Safety inspection
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="safety_inspection", nullable=false)
     */
    protected $safetyInspection;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * Set the is external
     *
     * @param unknown $isExternal
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
     * @return unknown
     */
    public function getIsExternal()
    {
        return $this->isExternal;
    }


    /**
     * Set the maintenance
     *
     * @param unknown $maintenance
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
     * @return unknown
     */
    public function getMaintenance()
    {
        return $this->maintenance;
    }


    /**
     * Set the safety inspection
     *
     * @param unknown $safetyInspection
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
     * @return unknown
     */
    public function getSafetyInspection()
    {
        return $this->safetyInspection;
    }

}
