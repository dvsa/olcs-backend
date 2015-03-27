<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trailer Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="trailer",
 *    indexes={
 *        @ORM\Index(name="ix_trailer_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_trailer_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_trailer_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_trailer_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Trailer implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OlbsKeyField,
        Traits\SpecifiedDateField,
        Traits\CustomVersionField;

    /**
     * Trailer no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="trailer_no", length=20, nullable=false)
     */
    protected $trailerNo;

    /**
     * Set the trailer no
     *
     * @param string $trailerNo
     * @return Trailer
     */
    public function setTrailerNo($trailerNo)
    {
        $this->trailerNo = $trailerNo;

        return $this;
    }

    /**
     * Get the trailer no
     *
     * @return string
     */
    public function getTrailerNo()
    {
        return $this->trailerNo;
    }
}
