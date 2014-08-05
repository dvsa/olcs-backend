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
 *        @ORM\Index(name="fk_change_of_entity_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_change_of_entity_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_change_of_entity_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class ChangeOfEntity implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Old licence no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="old_licence_no", length=45, nullable=false)
     */
    protected $oldLicenceNo;

    /**
     * Old organisation name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="old_organisation_name", length=45, nullable=false)
     */
    protected $oldOrganisationName;

    /**
     * Set the old licence no
     *
     * @param string $oldLicenceNo
     * @return \Olcs\Db\Entity\ChangeOfEntity
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
     * @return \Olcs\Db\Entity\ChangeOfEntity
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
