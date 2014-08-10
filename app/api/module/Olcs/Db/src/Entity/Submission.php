<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Submission Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="submission",
 *    indexes={
 *        @ORM\Index(name="fk_submission_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_submission_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_submission_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Submission implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOne,
        Traits\ClosedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Text
     *
     * @var string
     *
     * @ORM\Column(type="text", name="text", nullable=true)
     */
    protected $text;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * Set the text
     *
     * @param string $text
     * @return Submission
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

}
