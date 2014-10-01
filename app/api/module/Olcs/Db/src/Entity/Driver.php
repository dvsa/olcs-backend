<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Driver Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="driver",
 *    indexes={
 *        @ORM\Index(name="IDX_11667CD97CA35EB5", columns={"contact_details_id"}),
 *        @ORM\Index(name="IDX_11667CD965CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_11667CD9DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class Driver implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id", nullable=false)
     */
    protected $contactDetails;

    /**
     * Set the contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $contactDetails
     * @return Driver
     */
    public function setContactDetails($contactDetails)
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    /**
     * Get the contact details
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }
}
