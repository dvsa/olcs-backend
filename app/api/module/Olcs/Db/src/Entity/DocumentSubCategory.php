<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * DocumentSubCategory Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="document_sub_category",
 *    indexes={
 *        @ORM\Index(name="fk_document_sub_category_document_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_document_sub_category_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_document_sub_category_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class DocumentSubCategory implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CategoryManyToOne,
        Traits\Description255Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is scanned
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_scanned", nullable=false)
     */
    protected $isScanned = 0;

    /**
     * Display free text
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="display_free_text", nullable=false)
     */
    protected $displayFreeText = 0;

    /**
     * Set the is scanned
     *
     * @param string $isScanned
     * @return DocumentSubCategory
     */
    public function setIsScanned($isScanned)
    {
        $this->isScanned = $isScanned;

        return $this;
    }

    /**
     * Get the is scanned
     *
     * @return string
     */
    public function getIsScanned()
    {
        return $this->isScanned;
    }

    /**
     * Set the display free text
     *
     * @param string $displayFreeText
     * @return DocumentSubCategory
     */
    public function setDisplayFreeText($displayFreeText)
    {
        $this->displayFreeText = $displayFreeText;

        return $this;
    }

    /**
     * Get the display free text
     *
     * @return string
     */
    public function getDisplayFreeText()
    {
        return $this->displayFreeText;
    }
}
