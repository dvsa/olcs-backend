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
        Traits\LicenceManyToOne,
        Traits\CustomDeletedDateField,
        Traits\ViAction1Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", inversedBy="tradingNames")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     */
    protected $organisation;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=160, nullable=false)
     */
    protected $name;


    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return TradingName
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
     * Set the name
     *
     * @param string $name
     * @return TradingName
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
