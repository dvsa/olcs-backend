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
 *        @ORM\Index(name="ix_previous_conviction_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_previous_conviction_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_previous_conviction_title", columns={"title"})
 *    }
 * )
 */
class PreviousConviction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\BirthDateField,
        Traits\CategoryText1024Field,
        Traits\FamilyName35Field,
        Traits\Forename35Field,
        Traits\IdIdentity,
        Traits\Notes4000Field,
        Traits\Penalty255Field,
        Traits\TitleManyToOne,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="previousConvictions")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
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
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", inversedBy="previousConvictions")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManager;

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
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return PreviousConviction
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }
}
