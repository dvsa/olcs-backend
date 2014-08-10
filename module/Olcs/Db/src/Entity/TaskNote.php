<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TaskNote Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="task_note",
 *    indexes={
 *        @ORM\Index(name="fk_task_note_task1_idx", columns={"task_id"}),
 *        @ORM\Index(name="fk_task_note_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_task_note_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TaskNote implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TaskManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Note text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="note_text", length=1800, nullable=true)
     */
    protected $noteText;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * Set the note text
     *
     * @param string $noteText
     * @return TaskNote
     */
    public function setNoteText($noteText)
    {
        $this->noteText = $noteText;

        return $this;
    }

    /**
     * Get the note text
     *
     * @return string
     */
    public function getNoteText()
    {
        return $this->noteText;
    }

}
