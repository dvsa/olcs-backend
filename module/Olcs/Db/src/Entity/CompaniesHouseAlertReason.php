<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CompaniesHouseAlertReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_alert_reason",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_alert_reason_companies_house_alert_id", columns={"companies_house_alert_id"}),
 *        @ORM\Index(name="ix_companies_house_alert_reason_reason_type", columns={"reason_type"})
 *    }
 * )
 */
class CompaniesHouseAlertReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CustomVersionField;

    /**
     * Companies house alert
     *
     * @var \Olcs\Db\Entity\CompaniesHouseAlert
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CompaniesHouseAlert")
     * @ORM\JoinColumn(name="companies_house_alert_id", referencedColumnName="id", nullable=false)
     */
    protected $companiesHouseAlert;

    /**
     * Reason type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="reason_type", referencedColumnName="id", nullable=true)
     */
    protected $reasonType;

    /**
     * Set the companies house alert
     *
     * @param \Olcs\Db\Entity\CompaniesHouseAlert $companiesHouseAlert
     * @return CompaniesHouseAlertReason
     */
    public function setCompaniesHouseAlert($companiesHouseAlert)
    {
        $this->companiesHouseAlert = $companiesHouseAlert;

        return $this;
    }

    /**
     * Get the companies house alert
     *
     * @return \Olcs\Db\Entity\CompaniesHouseAlert
     */
    public function getCompaniesHouseAlert()
    {
        return $this->companiesHouseAlert;
    }

    /**
     * Set the reason type
     *
     * @param \Olcs\Db\Entity\RefData $reasonType
     * @return CompaniesHouseAlertReason
     */
    public function setReasonType($reasonType)
    {
        $this->reasonType = $reasonType;

        return $this;
    }

    /**
     * Get the reason type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getReasonType()
    {
        return $this->reasonType;
    }
}
