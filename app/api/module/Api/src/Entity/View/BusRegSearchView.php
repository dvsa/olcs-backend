<?php
/**
 * Bus Reg List View
 *
 * @NOTE: This walks and talks like an entity but be warned, it is backed
 * by a view. As such it is is nicely readable and searchable, but writes
 * are a no go.
 *
 * You'll notice that the entity has no setters; this is intentional to
 * try and prevent accidental writes. It's marked as readOnly too to
 * prevent doctrine including it in any flushes
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;

/**
 * Bus Reg List View
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="bus_reg_search_view")
 */
class BusRegSearchView implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * Service Number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="service_no")
     */
    protected $serviceNo;

    /**
     * Reg No
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reg_no")
     */
    protected $regNo;

    /**
     * Licence ID
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_id")
     */
    protected $licId;

    /**
     * Licence Number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_no")
     */
    protected $licNo;

    /**
     * Licence Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_status")
     */
    protected $licStatus;

    /**
     * Organisation ID
     *
     * @var integer
     *
     * @ORM\Column(type="integer", name="organisation_id")
     */
    protected $organisationId;

    /**
     * Organisation name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="organisation_name")
     */
    protected $organisationName;

    /**
     * Local Authority ID
     *
     * @var integer
     *
     * @ORM\Column(type="integer", name="local_authority_id")
     */
    protected $localAuthorityId;

    /**
     * Start Point
     *
     * @var string
     *
     * @ORM\Column(type="string", name="start_point")
     */
    protected $startPoint;

    /**
     * Start Point
     *
     * @var string
     *
     * @ORM\Column(type="string", name="finish_point")
     */
    protected $finishPoint;

    /**
     * Bus Reg Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="bus_reg_status_id")
     */
    protected $busRegStatus;

    /**
     * Bus Reg Status Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="bus_reg_status")
     */
    protected $busRegStatusDesc;

    /**
     * Route Number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="route_no")
     */
    protected $routeNo;

    /**
     * Variation Number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="variation_no")
     */
    protected $variationNo;

    /**
     * date_1st_reg
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="date_1st_reg")
     */
    protected $date1stReg;

    /**
     * get date1stReg
     *
     * @return \DateTime
     */
    public function getDate1stReg()
    {
        return $this->date1stReg;
    }

    /**
     * Set date1stReg
     *
     * @param \DateTime $date1stReg
     */
    public function setDate1stReg($date1stReg)
    {
        $this->date1stReg = $date1stReg;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets serviceNo
     *
     * @return string
     */
    public function getServiceNo()
    {
        return $this->serviceNo;
    }

    /**
     * Sets serviceNo
     *
     * @param string $serviceNo
     */
    public function setServiceNo($serviceNo)
    {
        $this->serviceNo = $serviceNo;
    }

    /**
     * Gets the regNo
     *
     * @return string
     */
    public function getRegNo()
    {
        return $this->regNo;
    }

    /**
     * Sets the regNo
     *
     * @param string $regNo
     */
    public function setRegNo($regNo)
    {
        $this->regNo = $regNo;
    }

    /**
     * Gets the licId
     *
     * @return string
     */
    public function getLicId()
    {
        return $this->licId;
    }

    /**
     * Sets the licId
     *
     * @param string $licId
     */
    public function setLicId($licId)
    {
        $this->licId = $licId;
    }

    /**
     * Gets the licNo
     *
     * @return string
     */
    public function getLicNo()
    {
        return $this->licNo;
    }

    /**
     * Sets the licNo
     *
     * @param string $licNo
     */
    public function setLicNo($licNo)
    {
        $this->licNo = $licNo;
    }

    /**
     * Get licStatus
     *
     * @return string
     */
    public function getLicStatus()
    {
        return $this->licStatus;
    }

    /**
     * Sets the licStatus
     *
     * @param string $licStatus
     */
    public function setLicStatus($licStatus)
    {
        $this->licStatus = $licStatus;
    }

    /**
     * Gets the organisationName
     *
     * @return string
     */
    public function getOrganisationName()
    {
        return $this->organisationName;
    }

    /**
     * @param string $organisationName
     */
    public function setOrganisationName($organisationName)
    {
        $this->organisationName = $organisationName;
    }

    /**
     * Gets the localAuthorityId
     *
     * @return integer
     */
    public function getLocalAuthorityId()
    {
        return $this->localAuthorityId;
    }

    /**
     * Sets the localAuthorityId
     *
     * @param integer $localAuthorityId
     */
    public function setLocalAuthorityId($localAuthorityId)
    {
        $this->localAuthorityId = $localAuthorityId;
    }

    /**
     * Get startPoint
     *
     * @return string
     */
    public function getStartPoint()
    {
        return $this->startPoint;
    }

    /**
     * Sets start point
     *
     * @param string $startPoint
     */
    public function setStartPoint($startPoint)
    {
        $this->startPoint = $startPoint;
    }

    /**
     * Get finish point
     *
     * @return string
     */
    public function getFinishPoint()
    {
        return $this->finishPoint;
    }

    /**
     * Set finish point
     *
     * @param string $finishPoint
     */
    public function setFinishPoint($finishPoint)
    {
        $this->finishPoint = $finishPoint;
    }

    /**
     * Get Bus Reg Status
     *
     * @return string
     */
    public function getBusRegStatus()
    {
        return $this->busRegStatus;
    }

    /**
     * Set Bus Reg Status
     *
     * @param string $busRegStatus
     */
    public function setBusRegStatus($busRegStatus)
    {
        $this->busRegStatus = $busRegStatus;
    }

    /**
     * Get bus reg status description
     *
     * @return string
     */
    public function getBusRegStatusDesc()
    {
        return $this->busRegStatusDesc;
    }

    /**
     * Set bus reg status description
     *
     * @param string $busRegStatusDesc
     */
    public function setBusRegStatusDesc($busRegStatusDesc)
    {
        $this->busRegStatusDesc = $busRegStatusDesc;
    }

    /**
     * Get route no
     *
     * @return string
     */
    public function getRouteNo()
    {
        return $this->routeNo;
    }

    /**
     * Set route no
     *
     * @param string $routeNo
     */
    public function setRouteNo($routeNo)
    {
        $this->routeNo = $routeNo;
    }

    /**
     * Get variation no
     *
     * @return string
     */
    public function getVariationNo()
    {
        return $this->variationNo;
    }

    /**
     * Set variation no
     * 
     * @param string $variationNo
     */
    public function setVariationNo($variationNo)
    {
        $this->variationNo = $variationNo;
    }
}
