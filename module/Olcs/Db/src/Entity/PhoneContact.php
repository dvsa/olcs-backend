<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PhoneContact Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="phone_contact",
 *    indexes={
 *        @ORM\Index(name="ix_phone_contact_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_phone_contact_phone_contact_type", columns={"phone_contact_type"}),
 *        @ORM\Index(name="ix_phone_contact_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_phone_contact_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_phone_contact_olbs_key_olbs_type_phone_contact_type", columns={"olbs_key","olbs_type","phone_contact_type"})
 *    }
 * )
 */
class PhoneContact implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\OlbsType32Field,
        Traits\CustomVersionField;

    /**
     * Contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", inversedBy="phoneContacts")
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id", nullable=false)
     */
    protected $contactDetails;

    /**
     * Details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="details", length=45, nullable=true)
     */
    protected $details;

    /**
     * Phone contact type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="phone_contact_type", referencedColumnName="id", nullable=false)
     */
    protected $phoneContactType;

    /**
     * Phone number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="phone_number", length=45, nullable=true)
     */
    protected $phoneNumber;

    /**
     * Set the contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $contactDetails
     * @return PhoneContact
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

    /**
     * Set the details
     *
     * @param string $details
     * @return PhoneContact
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get the details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set the phone contact type
     *
     * @param \Olcs\Db\Entity\RefData $phoneContactType
     * @return PhoneContact
     */
    public function setPhoneContactType($phoneContactType)
    {
        $this->phoneContactType = $phoneContactType;

        return $this;
    }

    /**
     * Get the phone contact type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPhoneContactType()
    {
        return $this->phoneContactType;
    }

    /**
     * Set the phone number
     *
     * @param string $phoneNumber
     * @return PhoneContact
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get the phone number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
}
