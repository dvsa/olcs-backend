<?php

namespace Dvsa\Olcs\Api\Entity\Template;

use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

/**
 * Template Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="template",
 *    indexes={
 *        @ORM\Index(name="ix_template_template_test_data_id", columns={"template_test_data_id"}),
 *        @ORM\Index(name="ix_template_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_template_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_name", columns={"locale","format","name"})
 *    }
 * )
 */
class Template extends AbstractTemplate
{
    /**
     * Get the test data associated with this template as an array
     *
     * @return array
     */
    public function getDecodedTestData()
    {
        return $this->templateTestData->getDecodedJson();
    }

    /**
     * Gets the category name from either the linked category entity or the internal category name attribute
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getComputedCategoryName()
    {
        if (!is_null($this->categoryName)) {
            return $this->categoryName;
        }

        if (!is_null($this->category)) {
            return $this->category->getDescription();
        }

        throw new RuntimeException('Invalid template data - category name and category id are both null');
    }
}
