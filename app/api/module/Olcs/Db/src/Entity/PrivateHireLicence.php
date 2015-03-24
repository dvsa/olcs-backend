<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PrivateHireLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="private_hire_licence",
 *    indexes={
 *        @ORM\Index(name="ix_private_hire_licence_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_private_hire_licence_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_private_hire_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_private_hire_licence_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_private_hire_licence_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class PrivateHireLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ContactDetailsManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="privateHireLicences")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Private hire licence no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="private_hire_licence_no", length=10, nullable=false)
     */
    protected $privateHireLicenceNo;

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return PrivateHireLicence
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
     * Set the private hire licence no
     *
     * @param string $privateHireLicenceNo
     * @return PrivateHireLicence
     */
    public function setPrivateHireLicenceNo($privateHireLicenceNo)
    {
        $this->privateHireLicenceNo = $privateHireLicenceNo;

        return $this;
    }

    /**
     * Get the private hire licence no
     *
     * @return string
     */
    public function getPrivateHireLicenceNo()
    {
        return $this->privateHireLicenceNo;
    }
}
