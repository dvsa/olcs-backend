<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * HintQuestion Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="hint_question",
 *    indexes={
 *        @ORM\Index(name="fk_hint_questions_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_hint_questions_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class HintQuestion implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Category no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="category_no", nullable=false)
     */
    protected $categoryNo;

    /**
     * Hint question
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hint_question", length=100, nullable=false)
     */
    protected $hintQuestion;

    /**
     * Set the category no
     *
     * @param int $categoryNo
     * @return HintQuestion
     */
    public function setCategoryNo($categoryNo)
    {
        $this->categoryNo = $categoryNo;

        return $this;
    }

    /**
     * Get the category no
     *
     * @return int
     */
    public function getCategoryNo()
    {
        return $this->categoryNo;
    }

    /**
     * Set the hint question
     *
     * @param string $hintQuestion
     * @return HintQuestion
     */
    public function setHintQuestion($hintQuestion)
    {
        $this->hintQuestion = $hintQuestion;

        return $this;
    }

    /**
     * Get the hint question
     *
     * @return string
     */
    public function getHintQuestion()
    {
        return $this->hintQuestion;
    }
}
