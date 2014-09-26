<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PreviousConviction Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="previous_conviction",
 *    indexes={
 *        @ORM\Index(name="IDX_7D04230D3E030ACD", columns={"application_id"})
 *    }
 * )
 */
class PreviousConviction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\ApplicationManyToOne,
        Traits\Title32Field,
        Traits\BirthDateField,
        Traits\CategoryText1024Field,
        Traits\Notes4000Field,
        Traits\Penalty255Field,
        Traits\CustomVersionField;

    /**
     * Conviction date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="conviction_date", nullable=true)
     */
    protected $convictionDate;

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
     * Set the conviction date
     *
     * @param \DateTime $convictionDate
     * @return PreviousConviction
     */
    public function setConvictionDate($convictionDate)
    {
        $this->convictionDate = $convictionDate;

        return $this;
    }

    /**
     * Get the conviction date
     *
     * @return \DateTime
     */
    public function getConvictionDate()
    {
        return $this->convictionDate;
    }

    /**
     * Set the forename
     *
     * @param string $forename
     * @return PreviousConviction
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
     * @return PreviousConviction
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
     * @return PreviousConviction
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
