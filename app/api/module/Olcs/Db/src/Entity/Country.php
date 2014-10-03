<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Country Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="country",
 *    indexes={
 *        @ORM\Index(name="IDX_5373C966DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_5373C96665CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Country implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id8Identity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Country desc
     *
     * @var string
     *
     * @ORM\Column(type="string", name="country_desc", length=200, nullable=true)
     */
    protected $countryDesc;

    /**
     * Is member state
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_member_state", nullable=false)
     */
    protected $isMemberState;

    /**
     * Set the country desc
     *
     * @param string $countryDesc
     * @return Country
     */
    public function setCountryDesc($countryDesc)
    {
        $this->countryDesc = $countryDesc;

        return $this;
    }

    /**
     * Get the country desc
     *
     * @return string
     */
    public function getCountryDesc()
    {
        return $this->countryDesc;
    }

    /**
     * Set the is member state
     *
     * @param string $isMemberState
     * @return Country
     */
    public function setIsMemberState($isMemberState)
    {
        $this->isMemberState = $isMemberState;

        return $this;
    }

    /**
     * Get the is member state
     *
     * @return string
     */
    public function getIsMemberState()
    {
        return $this->isMemberState;
    }
}
