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
 *        @ORM\Index(name="IDX_9B6F02C426EF07C9", columns={"licence_id"}),
 *        @ORM\Index(name="IDX_9B6F02C4DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_9B6F02C47CA35EB5", columns={"contact_details_id"}),
 *        @ORM\Index(name="IDX_9B6F02C465CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Workshop implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\ContactDetailsManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\RemovedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY", inversedBy="workshops")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Is external
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_external", nullable=false)
     */
    protected $isExternal;

    /**
     * Maintenance
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="maintenance", nullable=false)
     */
    protected $maintenance;

    /**
     * Safety inspection
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="safety_inspection", nullable=false)
     */
    protected $safetyInspection;

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
