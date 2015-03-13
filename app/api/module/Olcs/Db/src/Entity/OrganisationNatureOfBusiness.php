<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrganisationNatureOfBusiness Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="organisation_nature_of_business",
 *    indexes={
 *        @ORM\Index(name="ix_organisation_nature_of_business_ref_data_id", columns={"ref_data_id"}),
 *        @ORM\Index(name="ix_organisation_nature_of_business_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_organisation_nature_of_business_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_organisation_nature_of_business_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class OrganisationNatureOfBusiness implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", inversedBy="natureOfBusinesss")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Ref data
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="ref_data_id", referencedColumnName="id", nullable=false)
     */
    protected $refData;

    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return OrganisationNatureOfBusiness
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

    /**
     * Set the ref data
     *
     * @param \Olcs\Db\Entity\RefData $refData
     * @return OrganisationNatureOfBusiness
     */
    public function setRefData($refData)
    {
        $this->refData = $refData;

        return $this;
    }

    /**
     * Get the ref data
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRefData()
    {
        return $this->refData;
    }
}
