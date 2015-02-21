<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Financial evidence status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait FinancialEvidenceStatusField
{
    /**
     * Financial evidence status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="financial_evidence_status", nullable=true)
     */
    protected $financialEvidenceStatus;

    /**
     * Set the financial evidence status
     *
     * @param int $financialEvidenceStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setFinancialEvidenceStatus($financialEvidenceStatus)
    {
        $this->financialEvidenceStatus = $financialEvidenceStatus;

        return $this;
    }

    /**
     * Get the financial evidence status
     *
     * @return int
     */
    public function getFinancialEvidenceStatus()
    {
        return $this->financialEvidenceStatus;
    }
}
