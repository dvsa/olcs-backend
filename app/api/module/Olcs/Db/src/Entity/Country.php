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
        Traits\Id8Identity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_member_state", nullable=false)
     */
    protected $isMemberState = 0;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

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
     * @param unknown $isMemberState
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
     * @return unknown
     */
    public function getIsMemberState()
    {
        return $this->isMemberState;
    }

}
