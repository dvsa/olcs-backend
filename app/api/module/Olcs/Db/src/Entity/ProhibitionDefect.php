<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ProhibitionDefect Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="prohibition_defect",
 *    indexes={
 *        @ORM\Index(name="fk_prohoibition_defect_prohibition1_idx", columns={"prohibition_id"}),
 *        @ORM\Index(name="fk_prohoibition_defect_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_prohoibition_defect_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class ProhibitionDefect implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Notes4000Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Prohibition
     *
     * @var \Olcs\Db\Entity\Prohibition
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Prohibition", fetch="LAZY")
     * @ORM\JoinColumn(name="prohibition_id", referencedColumnName="id", nullable=false)
     */
    protected $prohibition;

    /**
     * Defect type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="defect_type", length=255, nullable=true)
     */
    protected $defectType;

    /**
     * Set the prohibition
     *
     * @param \Olcs\Db\Entity\Prohibition $prohibition
     * @return ProhibitionDefect
     */
    public function setProhibition($prohibition)
    {
        $this->prohibition = $prohibition;

        return $this;
    }

    /**
     * Get the prohibition
     *
     * @return \Olcs\Db\Entity\Prohibition
     */
    public function getProhibition()
    {
        return $this->prohibition;
    }


    /**
     * Set the defect type
     *
     * @param string $defectType
     * @return ProhibitionDefect
     */
    public function setDefectType($defectType)
    {
        $this->defectType = $defectType;

        return $this;
    }

    /**
     * Get the defect type
     *
     * @return string
     */
    public function getDefectType()
    {
        return $this->defectType;
    }

}
