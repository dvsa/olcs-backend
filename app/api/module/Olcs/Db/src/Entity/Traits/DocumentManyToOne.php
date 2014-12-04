<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Document many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait DocumentManyToOne
{
    /**
     * Document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=false)
     */
    protected $document;

    /**
     * Set the document
     *
     * @param \Olcs\Db\Entity\Document $document
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }
}
