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
     * Identifier - Id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=40)
     */
    protected $id;


    /**
     * Set the id
     *
     * @param string $id
     * @return AdminAreaTrafficArea
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

}
