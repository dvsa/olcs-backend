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
 *        @ORM\Index(name="IDX_851FEE735653D501", columns={"doc_template_id"}),
 *        @ORM\Index(name="IDX_851FEE73DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_851FEE73C1FDC79C", columns={"doc_bookmark_id"}),
 *        @ORM\Index(name="IDX_851FEE7365CF370E", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="doc_template_bookmark_unique", columns={"doc_template_id","doc_bookmark_id"})
 *    }
 * )
 */
class DocTemplateBookmark implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Doc bookmark
     *
     * @var \Olcs\Db\Entity\DocBookmark
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocBookmark", fetch="LAZY")
     * @ORM\JoinColumn(name="doc_bookmark_id", referencedColumnName="id", nullable=false)
     */
    protected $docBookmark;

    /**
     * Doc template
     *
     * @var \Olcs\Db\Entity\DocTemplate
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocTemplate", fetch="LAZY", inversedBy="docTemplateBookmarks")
     * @ORM\JoinColumn(name="doc_template_id", referencedColumnName="id", nullable=false)
     */
    protected $docTemplate;

    /**
     * Set the doc bookmark
     *
     * @param \Olcs\Db\Entity\DocBookmark $docBookmark
     * @return DocTemplateBookmark
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
