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
 *        @ORM\Index(name="fk_doc_paragraph_bookmark_doc_paragraph1_idx", columns={"doc_paragraph_id"}),
 *        @ORM\Index(name="fk_doc_paragraph_bookmark_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_doc_paragraph_bookmark_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_34C39149C1FDC79C", columns={"doc_bookmark_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="doc_bookmark_id", columns={"doc_bookmark_id","doc_paragraph_id"})
 *    }
 * )
 */
class DocParagraphBookmark implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\DocBookmarkManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Doc paragraph
     *
     * @var \Olcs\Db\Entity\DocParagraph
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocParagraph", fetch="LAZY")
     * @ORM\JoinColumn(name="doc_paragraph_id", referencedColumnName="id")
     */
    protected $docParagraph;


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
