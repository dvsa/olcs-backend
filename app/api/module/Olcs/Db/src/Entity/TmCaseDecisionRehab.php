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
 *        @ORM\Index(name="IDX_353BF96738237A80", columns={"rehab_measure_id"}),
 *        @ORM\Index(name="IDX_353BF96765CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_353BF967DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_353BF9672543F459", columns={"tm_case_decision_id"})
 *    }
 * )
 */
class TmCaseDecisionRehab implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\TmCaseDecisionManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Rehab measure
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
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
