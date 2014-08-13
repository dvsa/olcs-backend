<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmCaseDecisionUnfitness Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_case_decision_unfitness",
 *    indexes={
 *        @ORM\Index(name="fk_tm_case_decision_unfitness_tm_case_decision1_idx", columns={"tm_case_decision_id"}),
 *        @ORM\Index(name="fk_tm_case_decision_unfitness_ref_data1_idx", columns={"unfitness_reason_id"}),
 *        @ORM\Index(name="fk_tm_case_decision_unfitness_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_case_decision_unfitness_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TmCaseDecisionUnfitness implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TmCaseDecisionManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Unfitness reason
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="unfitness_reason_id", referencedColumnName="id")
     */
    protected $unfitnessReason;


    /**
     * Set the unfitness reason
     *
     * @param \Olcs\Db\Entity\RefData $unfitnessReason
     * @return TmCaseDecisionUnfitness
     */
    public function setUnfitnessReason($unfitnessReason)
    {
        $this->unfitnessReason = $unfitnessReason;

        return $this;
    }

    /**
     * Get the unfitness reason
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getUnfitnessReason()
    {
        return $this->unfitnessReason;
    }
}
