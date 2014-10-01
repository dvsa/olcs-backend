<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * DocParagraphBookmark Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_paragraph_bookmark",
 *    indexes={
 *        @ORM\Index(name="IDX_34C3914939BEDB8D", columns={"doc_paragraph_id"}),
 *        @ORM\Index(name="IDX_34C39149C1FDC79C", columns={"doc_bookmark_id"}),
 *        @ORM\Index(name="IDX_34C3914965CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_34C39149DE12AB56", columns={"created_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="doc_paragraph_bookmark_unique", columns={"doc_bookmark_id","doc_paragraph_id"})
 *    }
 * )
 */
class DocParagraphBookmark implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Doc bookmark
     *
     * @var \Olcs\Db\Entity\DocBookmark
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocBookmark", fetch="LAZY", inversedBy="docParagraphBookmarks")
     * @ORM\JoinColumn(name="doc_bookmark_id", referencedColumnName="id", nullable=false)
     */
    protected $docBookmark;

    /**
     * Doc paragraph
     *
     * @var \Olcs\Db\Entity\DocParagraph
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocParagraph", fetch="LAZY")
     * @ORM\JoinColumn(name="doc_paragraph_id", referencedColumnName="id", nullable=false)
     */
    protected $docParagraph;

    /**
     * Set the doc bookmark
     *
     * @param \Olcs\Db\Entity\DocBookmark $docBookmark
     * @return DocParagraphBookmark
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

    /**
     * Set the doc paragraph
     *
     * @param \Olcs\Db\Entity\DocParagraph $docParagraph
     * @return DocParagraphBookmark
     */
    public function setDocParagraph($docParagraph)
    {
        $this->docParagraph = $docParagraph;

        return $this;
    }

    /**
     * Get the doc paragraph
     *
     * @return \Olcs\Db\Entity\DocParagraph
     */
    public function getDocParagraph()
    {
        return $this->docParagraph;
    }
}
