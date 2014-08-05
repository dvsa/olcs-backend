<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Language Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="language")
 */
class Language implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Name100Field;

    /**
     * Identifier - Iso2
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="iso2", length=2)
     */
    protected $iso2;

    /**
     * Set the iso2
     *
     * @param string $iso2
     * @return \Olcs\Db\Entity\Language
     */
    public function setIso2($iso2)
    {
        $this->iso2 = $iso2;

        return $this;
    }

    /**
     * Get the iso2
     *
     * @return string
     */
    public function getIso2()
    {
        return $this->iso2;
    }
}
