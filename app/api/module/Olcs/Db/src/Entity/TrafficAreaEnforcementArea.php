<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrafficAreaEnforcementArea Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="traffic_area_enforcement_area",
 *    indexes={
 *        @ORM\Index(name="fk_TrafficAreaVehicleInspectorate_TrafficArea1_idx", 
 *            columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_TrafficAreaVehicleInspectorate_VehicleInspectorate1_idx", 
 *            columns={"enforcement_area_id"}),
 *        @ORM\Index(name="fk_traffic_area_enforcement_area_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_traffic_area_enforcement_area_user2_idx", 
 *            columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="traffic_area_enforcement_area_unique", 
 *            columns={"traffic_area_id","enforcement_area_id"})
 *    }
 * )
 */
class TrafficAreaEnforcementArea implements Interfaces\EntityInterface
{

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
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Enforcement area
     *
     * @var \Olcs\Db\Entity\EnforcementArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\EnforcementArea", fetch="LAZY")
     * @ORM\JoinColumn(name="enforcement_area_id", referencedColumnName="id", nullable=false)
     */
    protected $enforcementArea;

    /**
     * Traffic area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea", fetch="LAZY")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;
}
