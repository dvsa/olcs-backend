<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
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
 *        @ORM\Index(name="fk_doc_template_doc_process1_idx", columns={"doc_process_id"}),
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
        Traits\CategoryManyToOne,
        Traits\CreatedByManyToOne,
        Traits\DocumentManyToOne,
        Traits\Description255FieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Document sub category
     *
     * @var \Olcs\Db\Entity\DocumentSubCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocumentSubCategory", fetch="LAZY")
     * @ORM\JoinColumn(name="document_sub_category_id", referencedColumnName="id", nullable=false)
     */
    protected $documentSubCategory;

    /**
     * Doc process
     *
     * @var \Olcs\Db\Entity\DocProcess
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocProcess", fetch="LAZY")
     * @ORM\JoinColumn(name="doc_process_id", referencedColumnName="id", nullable=false)
     */
    protected $docProcess;

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
     * Set the doc process
     *
     * @param \Olcs\Db\Entity\DocProcess $docProcess
     * @return DocTemplate
     */
    public function setDocProcess($docProcess)
    {
        $this->docProcess = $docProcess;

        return $this;
    }

    /**
     * Get the doc process
     *
     * @return \Olcs\Db\Entity\DocProcess
     */
    public function getDocProcess()
    {
        return $this->docProcess;
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
}
