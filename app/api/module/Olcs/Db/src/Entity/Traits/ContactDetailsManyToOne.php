<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact details many to one trait
 *
 * Auto-Generated (Shared between 9 entities)
 */
trait ContactDetailsManyToOne
{
    /**
     * Contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id")
     */
    protected $contactDetails;

    /**
     * Set the contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $contactDetails
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
