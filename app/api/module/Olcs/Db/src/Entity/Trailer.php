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
 *        @ORM\Index(name="IDX_C691DC4E65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_C691DC4EDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_C691DC4E26EF07C9", columns={"licence_id"})
 *    }
 * )
 */
class Trailer implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\SpecifiedDateField,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
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
