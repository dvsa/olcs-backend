<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category text1024 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CategoryText1024Field
{
    /**
     * Category text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category_text", length=1024, nullable=true)
     */
    protected $categoryText;

    /**
     * Set the category text
     *
     * @param string $categoryText
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCategoryText($categoryText)
    {
        $this->categoryText = $categoryText;

        return $this;
    }

    /**
     * Get the category text
     *
     * @return string
     */
    public function getCategoryText()
    {
        return $this->categoryText;
    }
}
