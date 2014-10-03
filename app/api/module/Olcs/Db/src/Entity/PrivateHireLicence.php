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
 *        @ORM\Index(name="IDX_C0E79534DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_C0E795347CA35EB5", columns={"contact_details_id"}),
 *        @ORM\Index(name="IDX_C0E7953465CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_C0E7953426EF07C9", columns={"licence_id"})
 *    }
 * )
 */
class PrivateHireLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\ContactDetailsManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Private hire licence no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="private_hire_licence_no", length=10, nullable=false)
     */
    protected $privateHireLicenceNo;

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
