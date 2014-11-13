<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CommentField
{
    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column(type="text", name="comment", length=65535, nullable=true)
     */
    protected $comment;

    /**
     * Set the comment
     *
     * @param string $comment
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
