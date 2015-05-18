<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
 *        @ORM\Index(name="ix_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_country_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Country implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Country desc
     *
     * @var string
     *
     * @ORM\Column(type="string", name="country_desc", length=50, nullable=true)
     */
    protected $countryDesc;

    /**
     * Identifier - Id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=2)
     */
    protected $id;

    /**
     * Irfo psv auth
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\IrfoPsvAuth", mappedBy="countrys")
     */
    protected $irfoPsvAuths;

    /**
     * Is member state
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_member_state", nullable=false, options={"default": 0})
     */
    protected $isMemberState = 0;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->irfoPsvAuths = new ArrayCollection();
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
     * Set the id
     *
     * @param string $id
     * @return Country
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the irfo psv auth
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuths
     * @return Country
     */
    public function setIrfoPsvAuths($irfoPsvAuths)
    {
        $this->irfoPsvAuths = $irfoPsvAuths;

        return $this;
    }

    /**
     * Get the irfo psv auths
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrfoPsvAuths()
    {
        return $this->irfoPsvAuths;
    }

    /**
     * Add a irfo psv auths
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuths
     * @return Country
     */
    public function addIrfoPsvAuths($irfoPsvAuths)
    {
        if ($irfoPsvAuths instanceof ArrayCollection) {
            $this->irfoPsvAuths = new ArrayCollection(
                array_merge(
                    $this->irfoPsvAuths->toArray(),
                    $irfoPsvAuths->toArray()
                )
            );
        } elseif (!$this->irfoPsvAuths->contains($irfoPsvAuths)) {
            $this->irfoPsvAuths->add($irfoPsvAuths);
        }

        return $this;
    }

    /**
     * Remove a irfo psv auths
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuths
     * @return Country
     */
    public function removeIrfoPsvAuths($irfoPsvAuths)
    {
        if ($this->irfoPsvAuths->contains($irfoPsvAuths)) {
            $this->irfoPsvAuths->removeElement($irfoPsvAuths);
        }

        return $this;
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
