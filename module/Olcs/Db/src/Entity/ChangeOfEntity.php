<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ChangeOfEntity Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="change_of_entity",
 *    indexes={
 *        @ORM\Index(name="ix_change_of_entity_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_change_of_entity_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_change_of_entity_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_change_of_entity_licence_id", columns={"licence_id"}),
 *        @ORM\UniqueConstraint(name="uk_change_of_entity_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class ChangeOfEntity implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Old licence no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="old_licence_no", length=18, nullable=false)
     */
    protected $oldLicenceNo;

    /**
     * Old organisation name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="old_organisation_name", length=160, nullable=false)
     */
    protected $oldOrganisationName;

    /**
     * Set the old licence no
     *
     * @param string $oldLicenceNo
     * @return ChangeOfEntity
     */
    public function setOldLicenceNo($oldLicenceNo)
    {
        $this->oldLicenceNo = $oldLicenceNo;

        return $this;
    }

    /**
     * Get the old licence no
     *
     * @return string
     */
    public function getOldLicenceNo()
    {
        return $this->oldLicenceNo;
    }

    /**
     * Set the old organisation name
     *
     * @param string $oldOrganisationName
     * @return ChangeOfEntity
     */
    public function setOldOrganisationName($oldOrganisationName)
    {
        $this->oldOrganisationName = $oldOrganisationName;

        return $this;
    }

    /**
     * Get the old organisation name
     *
     * @return string
     */
    public function getOldOrganisationName()
    {
        return $this->oldOrganisationName;
    }
}
