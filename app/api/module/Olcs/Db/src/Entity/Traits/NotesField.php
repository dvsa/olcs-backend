<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notes field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait NotesField
{
    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="notes", nullable=true)
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
