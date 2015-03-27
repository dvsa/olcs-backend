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
 *        @ORM\Index(name="ix_prohibition_defect_prohibition_id", columns={"prohibition_id"}),
 *        @ORM\Index(name="ix_prohibition_defect_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_prohibition_defect_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_prohibition_defect_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class ProhibitionDefect implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Notes4000Field,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Defect type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="defect_type", length=255, nullable=true)
     */
    protected $defectType;

    /**
     * Prohibition
     *
     * @var \Olcs\Db\Entity\Prohibition
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Prohibition")
     * @ORM\JoinColumn(name="prohibition_id", referencedColumnName="id", nullable=false)
     */
    protected $prohibition;

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
}
