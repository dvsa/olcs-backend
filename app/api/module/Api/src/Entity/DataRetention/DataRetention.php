<?php

namespace Dvsa\Olcs\Api\Entity\DataRetention;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataRetention Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="data_retention",
 *    indexes={
 *        @ORM\Index(name="ix_entity_name", columns={"entity_name"}),
 *        @ORM\Index(name="ix_data_retention_rule_id", columns={"data_retention_rule_id"}),
 *        @ORM\Index(name="ix_delete_confirmation", columns={"action_confirmation"}),
 *        @ORM\Index(name="ix_deleted_date", columns={"deleted_date"}),
 *        @ORM\Index(name="ix_data_retention_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_data_retention_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_organisation_id", columns={"organisation_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_entity_name_entity_pk", columns={"entity_name","entity_pk"})
 *    }
 * )
 */
class DataRetention extends AbstractDataRetention
{
    /**
     * Mark for delete
     *
     * @return $this
     */
    public function markForDelete()
    {
        if (!$this->canMarkForDelete()) {
            $this->markForReview();
            return $this;
        }

        $this->actionedDate = new \DateTime();
        $this->actionConfirmation = true;

        return $this;
    }

    /**
     * Mark for review
     *
     * @return $this
     */
    public function markForReview()
    {
        $this->actionedDate = null;
        $this->actionConfirmation = false;

        return $this;
    }

    /**
     * Validate if a record can be marked as deleted or not
     *
     * @return bool
     */
    private function canMarkForDelete()
    {
        if ($this->getNextReviewDate() !== null) {
            return false;
        }

        return true;
    }
}
