<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Document sub category many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait DocumentSubCategoryManyToOne
{
    /**
     * Document sub category
     *
     * @var \Olcs\Db\Entity\DocumentSubCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocumentSubCategory")
     * @ORM\JoinColumn(name="document_sub_category_id", referencedColumnName="id")
     */
    protected $documentSubCategory;

    /**
     * Set the document sub category
     *
     * @param \Olcs\Db\Entity\DocumentSubCategory $documentSubCategory
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
}
