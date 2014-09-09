<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * AlphaSplit Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="alpha_split",
 *    indexes={
 *        @ORM\Index(name="fk_alpha_split_user1_idx", columns={"user_id"}),
 *        @ORM\Index(name="fk_alpha_split_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_alpha_split_user2_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_alpha_split_user3_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class AlphaSplit implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\UserManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\IsDeletedField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * From inclusive
     *
     * @var string
     *
     * @ORM\Column(type="string", name="from_inclusive", length=2, nullable=true)
     */
    protected $fromInclusive;

    /**
     * To inclusive
     *
     * @var string
     *
     * @ORM\Column(type="string", name="to_inclusive", length=2, nullable=true)
     */
    protected $toInclusive;

    /**
     * Set the from inclusive
     *
     * @param string $fromInclusive
     * @return AlphaSplit
     */
    public function setFromInclusive($fromInclusive)
    {
        $this->fromInclusive = $fromInclusive;

        return $this;
    }

    /**
     * Get the from inclusive
     *
     * @return string
     */
    public function getFromInclusive()
    {
        return $this->fromInclusive;
    }


    /**
     * Set the to inclusive
     *
     * @param string $toInclusive
     * @return AlphaSplit
     */
    public function setToInclusive($toInclusive)
    {
        $this->toInclusive = $toInclusive;

        return $this;
    }

    /**
     * Get the to inclusive
     *
     * @return string
     */
    public function getToInclusive()
    {
        return $this->toInclusive;
    }

}
