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
 *        @ORM\Index(name="ix_admin_area_traffic_area_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_admin_area_traffic_area_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_admin_area_traffic_area_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class AdminAreaTrafficArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\TrafficAreaManyToOne,
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
