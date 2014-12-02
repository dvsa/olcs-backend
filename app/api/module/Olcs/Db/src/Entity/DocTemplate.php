<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * DocTemplate Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="doc_template",
 *    indexes={
 *        @ORM\Index(name="fk_doc_template_document_sub_category1_idx", columns={"document_sub_category_id"}),
 *        @ORM\Index(name="fk_doc_template_document1_idx", columns={"document_id"}),
 *        @ORM\Index(name="fk_doc_template_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_doc_template_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_doc_template_document_category1_idx", columns={"category_id"})
 *    }
 * )
 */
class DocTemplate implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CategoryManyToOne,
        Traits\DocumentManyToOne,
        Traits\Description255Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Document sub category
     *
     * @var \Olcs\Db\Entity\DocumentSubCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocumentSubCategory")
     * @ORM\JoinColumn(name="document_sub_category_id", referencedColumnName="id", nullable=false)
     */
    protected $documentSubCategory;

    /**
     * Is ni
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_ni", nullable=false)
     */
    protected $isNi = 0;

    /**
     * Suppress from op
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="suppress_from_op", nullable=false)
     */
    protected $suppressFromOp;

    /**
     * Doc template bookmark
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\DocTemplateBookmark", mappedBy="docTemplate")
     */
    protected $docTemplateBookmarks;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->docTemplateBookmarks = new ArrayCollection();
    }

    /**
     * Set the document sub category
     *
     * @param \Olcs\Db\Entity\DocumentSubCategory $documentSubCategory
     * @return DocTemplate
     */
    public function setDocumentSubCategory($documentSubCategory)
    {
        $this->documentSubCategory = $documentSubCategory;

        return $this;
    }

    /**
     * Get the document sub category
     *
     * @return \Olcs\Db\Entity\DocumentSubCategory
     */
    public function getDocumentSubCategory()
    {
        return $this->documentSubCategory;
    }

    /**
     * Set the is ni
     *
     * @param string $isNi
     * @return DocTemplate
     */
    public function setIsNi($isNi)
    {
        $this->isNi = $isNi;

        return $this;
    }

    /**
     * Get the is ni
     *
     * @return string
     */
    public function getIsNi()
    {
        return $this->isNi;
    }

    /**
     * Set the suppress from op
     *
     * @param string $suppressFromOp
     * @return DocTemplate
     */
    public function setSuppressFromOp($suppressFromOp)
    {
        $this->suppressFromOp = $suppressFromOp;

        return $this;
    }

    /**
     * Get the suppress from op
     *
     * @return string
     */
    public function getSuppressFromOp()
    {
        return $this->suppressFromOp;
    }

    /**
     * Set the doc template bookmark
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $docTemplateBookmarks
     * @return DocTemplate
     */
    public function setDocTemplateBookmarks($docTemplateBookmarks)
    {
        $this->docTemplateBookmarks = $docTemplateBookmarks;

        return $this;
    }

    /**
     * Get the doc template bookmarks
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocTemplateBookmarks()
    {
        return $this->docTemplateBookmarks;
    }

    /**
     * Add a doc template bookmarks
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $docTemplateBookmarks
     * @return DocTemplate
     */
    public function addDocTemplateBookmarks($docTemplateBookmarks)
    {
        if ($docTemplateBookmarks instanceof ArrayCollection) {
            $this->docTemplateBookmarks = new ArrayCollection(
                array_merge(
                    $this->docTemplateBookmarks->toArray(),
                    $docTemplateBookmarks->toArray()
                )
            );
        } elseif (!$this->docTemplateBookmarks->contains($docTemplateBookmarks)) {
            $this->docTemplateBookmarks->add($docTemplateBookmarks);
        }

        return $this;
    }

    /**
     * Remove a doc template bookmarks
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $docTemplateBookmarks
     * @return DocTemplate
     */
    public function removeDocTemplateBookmarks($docTemplateBookmarks)
    {
        if ($this->docTemplateBookmarks->contains($docTemplateBookmarks)) {
            $this->docTemplateBookmarks->removeElement($docTemplateBookmarks);
        }

        return $this;
    }
}
