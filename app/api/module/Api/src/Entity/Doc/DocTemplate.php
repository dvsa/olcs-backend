<?php

namespace Dvsa\Olcs\Api\Entity\Doc;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\User\User;

/**
 * DocTemplate Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="doc_template",
 *    indexes={
 *        @ORM\Index(name="ix_doc_template_sub_category_id", columns={"sub_category_id"}),
 *        @ORM\Index(name="ix_doc_template_document_id", columns={"document_id"}),
 *        @ORM\Index(name="ix_doc_template_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_doc_template_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_doc_template_category_id", columns={"category_id"})
 *    }
 * )
 */
class DocTemplate extends AbstractDocTemplate
{
    const TEMPLATE_PATH_PREFIXES = [
        'root' => 'templates/',
        'ni' => 'templates/NI/',
        'gb' => 'templates/GB/',
        'image' => 'templates/Image/'
    ];

    /**
     * @param Category $category
     * @param SubCategory $subCategory
     * @param string $description
     * @param Document $document
     * @param bool $isNi
     * @param bool $suppressFromOp
     * @param string $deletedDate
     * @param User $createdBy
     * @return DocTemplate
     */
    public static function createNew(
        Category $category,
        SubCategory $subCategory,
        string $description,
        Document $document,
        bool $isNi,
        bool $suppressFromOp,
        $deletedDate,
        User $createdBy
    ) {
        $docTemplate = new self();
        $docTemplate->category = $category;
        $docTemplate->subCategory = $subCategory;
        $docTemplate->description = $description;
        $docTemplate->document = $document;
        $docTemplate->isNi = $isNi;
        $docTemplate->suppressFromOp = $suppressFromOp;
        $docTemplate->deletedDate = static::processDate($deletedDate);
        $docTemplate->category = $category;
        $docTemplate->createdBy = $createdBy;

        return $docTemplate;
    }

    /**
     * @param Category $category
     * @param SubCategory $subCategory
     * @param string $description
     * @param bool $isNi
     * @param bool $suppressFromOp
     * @return DocTemplate
     */
    public function updateMeta(
        Category $category,
        SubCategory $subCategory,
        string $description,
        bool $isNi,
        bool $suppressFromOp
    ) {
        $this->category = $category;
        $this->subCategory = $subCategory;
        $this->description = $description;
        $this->isNi = $isNi;
        $this->suppressFromOp = $suppressFromOp;

        return $this;
    }

    /**
     * @param Document $document
     * @return DocTemplate
     */
    public function updateDocument(
        Document $document
    ) {
        $this->document = $document;
        return $this;
    }
}
