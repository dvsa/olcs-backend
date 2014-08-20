<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notes4000 field trait
 *
 * Auto-Generated (Shared between 6 entities)
 */
trait Notes4000Field
{
    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=4000, nullable=true)
     */
    protected $notes;

    /**
     * Set the notes
     *
     * @param string $notes
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
