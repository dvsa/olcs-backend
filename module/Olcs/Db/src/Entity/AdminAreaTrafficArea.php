<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * AdminAreaTrafficArea Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="admin_area_traffic_area",
 *    indexes={
 *        @ORM\Index(name="fk_admin_area_traffic_area_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_admin_area_traffic_area_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_admin_area_traffic_area_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class AdminAreaTrafficArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Admin area
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="admin_area", length=40)
     */
    protected $adminArea;

    /**
     * Set the admin area
     *
     * @param string $adminArea
     * @return \Olcs\Db\Entity\AdminAreaTrafficArea
     */
    public function setAdminArea($adminArea)
    {
        $this->adminArea = $adminArea;

        return $this;
    }

    /**
     * Get the admin area
     *
     * @return string
     */
    public function getAdminArea()
    {
        return $this->adminArea;
    }
}
