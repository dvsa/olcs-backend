<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * DocParagraph Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_paragraph",
 *    indexes={
 *        @ORM\Index(name="ix_doc_paragraph_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_doc_paragraph_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class DocParagraph implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Para text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="para_text", length=1000, nullable=true)
     */
    protected $paraText;

    /**
     * Para title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="para_title", length=255, nullable=false)
     */
    protected $paraTitle;

    /**
     * Set the para text
     *
     * @param string $paraText
     * @return DocParagraph
     */
    public function setParaText($paraText)
    {
        $this->paraText = $paraText;

        return $this;
    }

    /**
     * Get the para text
     *
     * @return string
     */
    public function getParaText()
    {
        return $this->paraText;
    }

    /**
     * Set the para title
     *
     * @param string $paraTitle
     * @return DocParagraph
     */
    public function setParaTitle($paraTitle)
    {
        $this->paraTitle = $paraTitle;

        return $this;
    }

    /**
     * Get the para title
     *
     * @return string
     */
    public function getParaTitle()
    {
        return $this->paraTitle;
    }
}
