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
 *        @ORM\Index(name="fk_previous_convictions_application1_idx", columns={"application_id"})
 *    }
 * )
 */
class PreviousConviction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\BirthDateField,
        Traits\CategoryText1024Field,
        Traits\IdIdentity,
        Traits\Notes4000Field,
        Traits\Penalty255Field,
        Traits\Title32Field,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="previousConvictions")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

    /**
     * Conviction date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="conviction_date", nullable=true)
     */
    protected $convictionDate;

    /**
     * Court fpn
     *
     * @var string
     *
     * @ORM\Column(type="string", name="court_fpn", length=70, nullable=true)
     */
    protected $courtFpn;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=35, nullable=false)
     */
    protected $familyName;

    /**
     * Forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="forename", length=35, nullable=false)
     */
    protected $forename;

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return PreviousConviction
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

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
}
