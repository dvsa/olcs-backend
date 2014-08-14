<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * DocTemplateBookmark Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_template_bookmark",
 *    indexes={
 *        @ORM\Index(name="fk_doc_template_bookmark_doc_bookmark1_idx", columns={"doc_bookmark_id"}),
 *        @ORM\Index(name="fk_doc_template_bookmark_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_doc_template_bookmark_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_851FEE735653D501", columns={"doc_template_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="doc_template_id", columns={"doc_template_id","doc_bookmark_id"})
 *    }
 * )
 */
class DocTemplateBookmark implements Interfaces\EntityInterface
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
     * Doc template
     *
     * @var \Olcs\Db\Entity\DocTemplate
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocTemplate", fetch="LAZY")
     * @ORM\JoinColumn(name="doc_template_id", referencedColumnName="id", nullable=false)
     */
    protected $docTemplate;


    /**
     * Set the doc template
     *
     * @param \Olcs\Db\Entity\DocTemplate $docTemplate
     * @return DocTemplateBookmark
     */
    public function setDocTemplate($docTemplate)
    {
        $this->docTemplate = $docTemplate;

        return $this;
    }

    /**
     * Get the doc template
     *
     * @return \Olcs\Db\Entity\DocTemplate
     */
    public function getDocTemplate()
    {
        return $this->docTemplate;
    }
}
