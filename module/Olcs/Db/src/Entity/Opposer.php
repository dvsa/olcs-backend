<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Opposer Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="opposer",
 *    indexes={
 *        @ORM\Index(name="fk_opposer_contact_details1_idx", columns={"contact_details_id"}),
 *        @ORM\Index(name="fk_opposer_ref_data1_idx", columns={"opposer_type"}),
 *        @ORM\Index(name="fk_opposer_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_opposer_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Opposer implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_details_id", referencedColumnName="id", nullable=false)
     */
    protected $contactDetails;

    /**
     * Opposer type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="opposer_type", referencedColumnName="id", nullable=true)
     */
    protected $opposerType;

    /**
     * Set the contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $contactDetails
     * @return Opposer
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
     * Set the opposer type
     *
     * @param \Olcs\Db\Entity\RefData $opposerType
     * @return Opposer
     */
    public function setOpposerType($opposerType)
    {
        $this->opposerType = $opposerType;

        return $this;
    }

    /**
     * Get the opposer type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getOpposerType()
    {
        return $this->opposerType;
    }
}
