<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TradingName Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="trading_name",
 *    indexes={
 *        @ORM\Index(name="fk_trading_name_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_trading_name_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_trading_name_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_trading_name_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TradingName implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\OrganisationManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomDeletedDateField,
        Traits\ViAction1Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=160, nullable=false)
     */
    protected $name;

    /**
     * Set the name
     *
     * @param string $name
     * @return \Olcs\Db\Entity\TradingName
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
