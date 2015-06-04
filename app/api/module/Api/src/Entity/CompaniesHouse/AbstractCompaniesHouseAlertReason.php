<?php

namespace Dvsa\Olcs\Api\Entity\CompaniesHouse;

use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\JsonSerializableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CompaniesHouseAlertReason Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="companies_house_alert_reason",
 *    indexes={
 *        @ORM\Index(name="ix_companies_house_alert_reason_companies_house_alert_id",
     *     columns={"companies_house_alert_id"}),
 *        @ORM\Index(name="ix_companies_house_alert_reason_reason_type", columns={"reason_type"})
 *    }
 * )
 */
abstract class AbstractCompaniesHouseAlertReason implements \JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * Companies house alert
     *
     * @var \Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert",
     *     fetch="LAZY",
     *     inversedBy="reasons"
     * )
     * @ORM\JoinColumn(name="companies_house_alert_id", referencedColumnName="id", nullable=false)
     */
    protected $companiesHouseAlert;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Reason type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="reason_type", referencedColumnName="id", nullable=true)
     */
    protected $reasonType;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the companies house alert
     *
     * @param \Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert $companiesHouseAlert
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
     * @return \Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert
     */
    public function getCompaniesHouseAlert()
    {
        return $this->companiesHouseAlert;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return CompaniesHouseAlertReason
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the reason type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $reasonType
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
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getReasonType()
    {
        return $this->reasonType;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return CompaniesHouseAlertReason
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }



    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
