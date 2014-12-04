<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OppositionGrounds Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="opposition_grounds",
 *    indexes={
 *        @ORM\Index(name="fk_opposition_ground_opposition1_idx", columns={"opposition_id"}),
 *        @ORM\Index(name="fk_opposition_ground_ref_data1_idx", columns={"grounds"}),
 *        @ORM\Index(name="fk_opposition_grounds_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_opposition_grounds_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class OppositionGrounds implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Grounds
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="grounds", referencedColumnName="id", nullable=false)
     */
    protected $grounds;

    /**
     * Opposition
     *
     * @var \Olcs\Db\Entity\Opposition
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposition", fetch="LAZY", inversedBy="grounds")
     * @ORM\JoinColumn(name="opposition_id", referencedColumnName="id", nullable=false)
     */
    protected $opposition;

    /**
     * Is representation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_representation", nullable=false)
     */
    protected $isRepresentation = 0;

    /**
     * Set the grounds
     *
     * @param \Olcs\Db\Entity\RefData $grounds
     * @return OppositionGrounds
     */
    public function setGrounds($grounds)
    {
        $this->grounds = $grounds;

        return $this;
    }

    /**
     * Get the grounds
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getGrounds()
    {
        return $this->grounds;
    }

    /**
     * Set the opposition
     *
     * @param \Olcs\Db\Entity\Opposition $opposition
     * @return OppositionGrounds
     */
    public function setOpposition($opposition)
    {
        $this->opposition = $opposition;

        return $this;
    }

    /**
     * Get the opposition
     *
     * @return \Olcs\Db\Entity\Opposition
     */
    public function getOpposition()
    {
        return $this->opposition;
    }

    /**
     * Set the is representation
     *
     * @param string $isRepresentation
     * @return OppositionGrounds
     */
    public function setIsRepresentation($isRepresentation)
    {
        $this->isRepresentation = $isRepresentation;

        return $this;
    }

    /**
     * Get the is representation
     *
     * @return string
     */
    public function getIsRepresentation()
    {
        return $this->isRepresentation;
    }
}
