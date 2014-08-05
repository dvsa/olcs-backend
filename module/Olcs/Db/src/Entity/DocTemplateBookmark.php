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
 *    }
 * )
 */
class DocTemplateBookmark implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\DocBookmarkOneToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Doc template
     *
     * @var \Olcs\Db\Entity\DocTemplate
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\DocTemplate")
     * @ORM\JoinColumn(name="doc_template_id", referencedColumnName="id")
     */
    protected $docTemplate;

    /**
     * Set the doc template
     *
     * @param \Olcs\Db\Entity\DocTemplate $docTemplate
     * @return \Olcs\Db\Entity\DocTemplateBookmark
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
