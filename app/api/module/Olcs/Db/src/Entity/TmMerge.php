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
 *        @ORM\Index(name="ix_tm_merge_tm_from_id", columns={"tm_from_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_to_id", columns={"tm_to_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_application_id", columns={"tm_application_id"}),
 *        @ORM\Index(name="ix_tm_merge_tm_licence_id", columns={"tm_licence_id"}),
 *        @ORM\Index(name="ix_tm_merge_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_tm_merge_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_tm_merge_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TmMerge implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

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
     * Tm from
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager")
     * @ORM\JoinColumn(name="tm_from_id", referencedColumnName="id", nullable=false)
     */
    protected $tmFrom;

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
     * Tm to
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager")
     * @ORM\JoinColumn(name="tm_to_id", referencedColumnName="id", nullable=false)
     */
    protected $tmTo;

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
}
