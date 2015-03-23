<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrganisationPerson Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="organisation_person",
 *    indexes={
 *        @ORM\Index(name="ix_organisation_person_person_id", columns={"person_id"}),
 *        @ORM\Index(name="ix_organisation_person_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_organisation_person_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_organisation_person_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_organisation_person_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class OrganisationPerson implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\PersonManyToOne,
        Traits\Position45Field,
        Traits\CustomVersionField;

    /**
     * Added date
     *
     * @var string
     *
     * @ORM\Column(type="string", name="added_date", length=45, nullable=true)
     */
    protected $addedDate;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", inversedBy="organisationPersons")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Set the added date
     *
     * @param string $addedDate
     * @return OrganisationPerson
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return string
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return OrganisationPerson
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }
}
