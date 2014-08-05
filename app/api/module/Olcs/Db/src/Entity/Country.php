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
 *        @ORM\Index(name="fk_country_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_country_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Country implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Country code
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="country_code", length=8)
     */
    protected $countryCode;

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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_member_state", nullable=false)
     */
    protected $isMemberState = 0;

    /**
     * Set the country code
     *
     * @param string $countryCode
     * @return \Olcs\Db\Entity\Country
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get the country code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set the country desc
     *
     * @param string $countryDesc
     * @return \Olcs\Db\Entity\Country
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
     * @param boolean $isMemberState
     * @return \Olcs\Db\Entity\Country
     */
    public function setIsMemberState($isMemberState)
    {
        $this->isMemberState = $isMemberState;

        return $this;
    }

    /**
     * Get the is member state
     *
     * @return boolean
     */
    public function getIsMemberState()
    {
        return $this->isMemberState;
    }
}
