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
class BusRegListView implements Interfaces\EntityInterface
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
    protected $licenceId;

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
    protected $licenceStatus;

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
    protected $routeNumber;

    /**
     * Variation Number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="variation_no")
     */
    protected $variationNumber;
}
