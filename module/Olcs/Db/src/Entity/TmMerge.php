<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TmMerge Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="tm_merge",
 *    indexes={
 *        @ORM\Index(name="fk_tm_merge_transport_manager1_idx", columns={"tm_from_id"}),
 *        @ORM\Index(name="fk_tm_merge_transport_manager2_idx", columns={"tm_to_id"}),
 *        @ORM\Index(name="fk_tm_merge_transport_manager_application1_idx", columns={"tm_application_id"}),
 *        @ORM\Index(name="fk_tm_merge_transport_manager_licence1_idx", columns={"tm_licence_id"}),
 *        @ORM\Index(name="fk_tm_merge_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_merge_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TmMerge implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Tm licence
     *
     * @var \Olcs\Db\Entity\TransportManagerLicence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManagerLicence")
     * @ORM\JoinColumn(name="tm_licence_id", referencedColumnName="id", nullable=true)
     */
    protected $tmLicence;

    /**
     * Tm application
     *
     * @var \Olcs\Db\Entity\TransportManagerApplication
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManagerApplication")
     * @ORM\JoinColumn(name="tm_application_id", referencedColumnName="id", nullable=true)
     */
    protected $tmApplication;

    /**
     * Tm to
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager")
     * @ORM\JoinColumn(name="tm_to_id", referencedColumnName="id", nullable=false)
     */
    protected $tmTo;

    /**
     * Tm from
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager")
     * @ORM\JoinColumn(name="tm_from_id", referencedColumnName="id", nullable=false)
     */
    protected $tmFrom;

    /**
     * Set the tm licence
     *
     * @param \Olcs\Db\Entity\TransportManagerLicence $tmLicence
     * @return TmMerge
     */
    public function setTmLicence($tmLicence)
    {
        $this->tmLicence = $tmLicence;

        return $this;
    }

    /**
     * Get the tm licence
     *
     * @return \Olcs\Db\Entity\TransportManagerLicence
     */
    public function getTmLicence()
    {
        return $this->tmLicence;
    }

    /**
     * Set the tm application
     *
     * @param \Olcs\Db\Entity\TransportManagerApplication $tmApplication
     * @return TmMerge
     */
    public function setTmApplication($tmApplication)
    {
        $this->tmApplication = $tmApplication;

        return $this;
    }

    /**
     * Get the tm application
     *
     * @return \Olcs\Db\Entity\TransportManagerApplication
     */
    public function getTmApplication()
    {
        return $this->tmApplication;
    }

    /**
     * Set the tm to
     *
     * @param \Olcs\Db\Entity\TransportManager $tmTo
     * @return TmMerge
     */
    public function setTmTo($tmTo)
    {
        $this->tmTo = $tmTo;

        return $this;
    }

    /**
     * Get the tm to
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTmTo()
    {
        return $this->tmTo;
    }

    /**
     * Set the tm from
     *
     * @param \Olcs\Db\Entity\TransportManager $tmFrom
     * @return TmMerge
     */
    public function setTmFrom($tmFrom)
    {
        $this->tmFrom = $tmFrom;

        return $this;
    }

    /**
     * Get the tm from
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTmFrom()
    {
        return $this->tmFrom;
    }
}
