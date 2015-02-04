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
namespace Olcs\Db\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Interfaces;

/**
 * Bus Reg List View
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="bus_reg_search_view")
 */
class BusRegSearchView implements Interfaces\EntityInterface
{
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
     * Organisation name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="organisation_name")
     */
    protected $organisationName;

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
     * @ORM\Column(type="string", name="bus_reg_status")
     */
    protected $busRegStatus;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getServiceNo()
    {
        return $this->serviceNo;
    }

    /**
     * @param string $serviceNo
     */
    public function setServiceNo($serviceNo)
    {
        $this->serviceNo = $serviceNo;
    }

    /**
     * @return string
     */
    public function getRegNo()
    {
        return $this->regNo;
    }

    /**
     * @param string $regNo
     */
    public function setRegNo($regNo)
    {
        $this->regNo = $regNo;
    }

    /**
     * @return string
     */
    public function getLicId()
    {
        return $this->licId;
    }

    /**
     * @param string $licId
     */
    public function setLicId($licId)
    {
        $this->licId = $licId;
    }

    /**
     * @return string
     */
    public function getLicNo()
    {
        return $this->licNo;
    }

    /**
     * @param string $licNo
     */
    public function setLicNo($licNo)
    {
        $this->licNo = $licNo;
    }

    /**
     * @return string
     */
    public function getLicStatus()
    {
        return $this->licStatus;
    }

    /**
     * @param string $licStatus
     */
    public function setLicStatus($licStatus)
    {
        $this->licStatus = $licStatus;
    }

    /**
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
     * @return string
     */
    public function getStartPoint()
    {
        return $this->startPoint;
    }

    /**
     * @param string $startPoint
     */
    public function setStartPoint($startPoint)
    {
        $this->startPoint = $startPoint;
    }

    /**
     * @return string
     */
    public function getFinishPoint()
    {
        return $this->finishPoint;
    }

    /**
     * @param string $finishPoint
     */
    public function setFinishPoint($finishPoint)
    {
        $this->finishPoint = $finishPoint;
    }

    /**
     * @return string
     */
    public function getBusRegStatus()
    {
        return $this->busRegStatus;
    }

    /**
     * @param string $busRegStatus
     */
    public function setBusRegStatus($busRegStatus)
    {
        $this->busRegStatus = $busRegStatus;
    }

    /**
     * @return string
     */
    public function getRouteNo()
    {
        return $this->routeNo;
    }

    /**
     * @param string $routeNo
     */
    public function setRouteNo($routeNo)
    {
        $this->routeNo = $routeNo;
    }

    /**
     * @return string
     */
    public function getVariationNo()
    {
        return $this->variationNo;
    }

    /**
     * @param string $variationNo
     */
    public function setVariationNo($variationNo)
    {
        $this->variationNo = $variationNo;
    }
}
