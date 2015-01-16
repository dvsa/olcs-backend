<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TmCaseDecisionRehab Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="tm_case_decision_rehab",
 *    indexes={
 *        @ORM\Index(name="fk_tm_case_decision_rehab_tm_case_decision1_idx", columns={"tm_case_decision_id"}),
 *        @ORM\Index(name="fk_tm_case_decision_rehab_ref_data1_idx", columns={"rehab_measure_id"}),
 *        @ORM\Index(name="fk_tm_case_decision_rehab_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_tm_case_decision_rehab_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TmCaseDecisionRehab implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\TmCaseDecisionManyToOne,
        Traits\CustomVersionField;

    /**
     * Rehab measure
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="rehab_measure_id", referencedColumnName="id", nullable=false)
     */
    protected $rehabMeasure;

    /**
     * Set the rehab measure
     *
     * @param \Olcs\Db\Entity\RefData $rehabMeasure
     * @return TmCaseDecisionRehab
     */
    public function setRehabMeasure($rehabMeasure)
    {
        $this->rehabMeasure = $rehabMeasure;

        return $this;
    }

    /**
     * Get the rehab measure
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRehabMeasure()
    {
        return $this->rehabMeasure;
    }
}
