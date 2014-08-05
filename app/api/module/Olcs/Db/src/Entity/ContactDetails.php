<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ContactDetails Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="contact_details",
 *    indexes={
 *        @ORM\Index(name="fk_contact_details_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_contact_details_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_contact_details_person1_idx", columns={"person_id"}),
 *        @ORM\Index(name="fk_contact_details_address1_idx", columns={"address_id"}),
 *        @ORM\Index(name="fk_contact_details_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_contact_details_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_contact_details_ref_data1_idx", columns={"contact_type"})
 *    }
 * )
 */
class ContactDetails implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\ContactTypeManyToOne,
        Traits\CreatedByManyToOne,
        Traits\AddressManyToOne,
        Traits\OrganisationManyToOne,
        Traits\PersonManyToOne,
        Traits\LicenceManyToOne,
        Traits\EmailAddress60Field,
        Traits\Description255FieldAlt1,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Fao
     *
     * @var string
     *
     * @ORM\Column(type="string", name="fao", length=90, nullable=true)
     */
    protected $fao;

    /**
     * Forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="forename", length=40, nullable=true)
     */
    protected $forename;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=40, nullable=true)
     */
    protected $familyName;

    /**
     * Written permission to engage
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="written_permission_to_engage", nullable=false)
     */
    protected $writtenPermissionToEngage = 0;

    /**
     * Set the fao
     *
     * @param string $fao
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function setFao($fao)
    {
        $this->fao = $fao;

        return $this;
    }

    /**
     * Get the fao
     *
     * @return string
     */
    public function getFao()
    {
        return $this->fao;
    }

    /**
     * Set the forename
     *
     * @param string $forename
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get the forename
     *
     * @return string
     */
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * Set the family name
     *
     * @param string $familyName
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get the family name
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set the written permission to engage
     *
     * @param boolean $writtenPermissionToEngage
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function setWrittenPermissionToEngage($writtenPermissionToEngage)
    {
        $this->writtenPermissionToEngage = $writtenPermissionToEngage;

        return $this;
    }

    /**
     * Get the written permission to engage
     *
     * @return boolean
     */
    public function getWrittenPermissionToEngage()
    {
        return $this->writtenPermissionToEngage;
    }
}
