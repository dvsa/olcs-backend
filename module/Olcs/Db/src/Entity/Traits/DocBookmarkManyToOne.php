<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doc bookmark many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait DocBookmarkManyToOne
{
    /**
     * Doc bookmark
     *
     * @var \Olcs\Db\Entity\DocBookmark
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocBookmark")
     * @ORM\JoinColumn(name="doc_bookmark_id", referencedColumnName="id")
     */
    protected $docBookmark;

    /**
     * Set the doc bookmark
     *
     * @param \Olcs\Db\Entity\DocBookmark $docBookmark
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDocBookmark($docBookmark)
    {
        $this->docBookmark = $docBookmark;

        return $this;
    }

    /**
     * Get the doc bookmark
     *
     * @return \Olcs\Db\Entity\DocBookmark
     */
    public function getDocBookmark()
    {
        return $this->docBookmark;
    }

}
