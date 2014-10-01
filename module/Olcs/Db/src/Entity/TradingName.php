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
 *        @ORM\Index(name="IDX_18562FA9E6B1585", columns={"organisation_id"}),
 *        @ORM\Index(name="IDX_18562FA65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_18562FADE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_18562FA26EF07C9", columns={"licence_id"})
 *    }
 * )
 */
class TradingName implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOneAlt1,
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
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY", inversedBy="tradingNames")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=true)
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
