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
 *        @ORM\Index(name="ix_opposer_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_opposer_opposer_type", columns={"opposer_type"}),
 *        @ORM\Index(name="ix_opposer_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_opposer_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_opposer_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
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
        Traits\OlbsKeyField,
        Traits\OlbsType32Field,
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
