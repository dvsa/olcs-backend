<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * DocBookmark Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_bookmark",
 *    indexes={
 *        @ORM\Index(name="IDX_BBD68F46DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_BBD68F4665CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class DocBookmark implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=50, nullable=false)
     */
    protected $name;

    /**
     * Set the name
     *
     * @param string $name
     * @return DocBookmark
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
