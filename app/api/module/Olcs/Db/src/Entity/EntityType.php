<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * EntityType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="entity_type",
 *    indexes={
 *        @ORM\Index(name="fk_entity_type_template1_idx", columns={"current_template_id"})
 *    }
 * )
 */
class EntityType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\Name45Field;

    /**
     * Current template
     *
     * @var \Olcs\Db\Entity\Template
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Template", fetch="LAZY")
     * @ORM\JoinColumn(name="current_template_id", referencedColumnName="id", nullable=false)
     */
    protected $currentTemplate;

    /**
     * Set the current template
     *
     * @param \Olcs\Db\Entity\Template $currentTemplate
     * @return EntityType
     */
    public function setCurrentTemplate($currentTemplate)
    {
        $this->currentTemplate = $currentTemplate;

        return $this;
    }

    /**
     * Get the current template
     *
     * @return \Olcs\Db\Entity\Template
     */
    public function getCurrentTemplate()
    {
        return $this->currentTemplate;
    }

}
