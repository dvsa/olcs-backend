<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PreviousConvictions Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="previous_convictions",
 *    indexes={
 *        @ORM\Index(name="fk_previous_convictions_application1_idx", columns={"application_id"})
 *    }
 * )
 */
class PreviousConvictions implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ApplicationManyToOne,
        Traits\BirthDateField,
        Traits\CategoryText1024Field,
        Traits\Notes4000Field,
        Traits\Penalty255Field;

    /**
     * Previous convictionscol
     *
     * @var string
     *
     * @ORM\Column(type="string", name="previous_convictionscol", length=45, nullable=true)
     */
    protected $previousConvictionscol;

    /**
     * Person title id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="person_title_id", nullable=true)
     */
    protected $personTitleId;

    /**
     * Forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="forename", length=35, nullable=false)
     */
    protected $forename;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=35, nullable=false)
     */
    protected $familyName;

    /**
     * Court fpn
     *
     * @var string
     *
     * @ORM\Column(type="string", name="court_fpn", length=70, nullable=true)
     */
    protected $courtFpn;


    /**
     * Set the previous convictionscol
     *
     * @param string $previousConvictionscol
     * @return PreviousConvictions
     */
    public function setPreviousConvictionscol($previousConvictionscol)
    {
        $this->previousConvictionscol = $previousConvictionscol;

        return $this;
    }

    /**
     * Get the previous convictionscol
     *
     * @return string
     */
    public function getPreviousConvictionscol()
    {
        return $this->previousConvictionscol;
    }


    /**
     * Set the person title id
     *
     * @param int $personTitleId
     * @return PreviousConvictions
     */
    public function setPersonTitleId($personTitleId)
    {
        $this->personTitleId = $personTitleId;

        return $this;
    }

    /**
     * Get the person title id
     *
     * @return int
     */
    public function getPersonTitleId()
    {
        return $this->personTitleId;
    }


    /**
     * Set the forename
     *
     * @param string $forename
     * @return PreviousConvictions
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get the forename
     *
     * @return string
     */
    public function getForename()
    {
        return $this->forename;
    }


    /**
     * Set the family name
     *
     * @param string $familyName
     * @return PreviousConvictions
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get the family name
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }


    /**
     * Set the court fpn
     *
     * @param string $courtFpn
     * @return PreviousConvictions
     */
    public function setCourtFpn($courtFpn)
    {
        $this->courtFpn = $courtFpn;

        return $this;
    }

    /**
     * Get the court fpn
     *
     * @return string
     */
    public function getCourtFpn()
    {
        return $this->courtFpn;
    }

}
