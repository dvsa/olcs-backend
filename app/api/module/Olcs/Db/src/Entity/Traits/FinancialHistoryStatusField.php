<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Financial history status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait FinancialHistoryStatusField
{
    /**
     * Financial history status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="financial_history_status", nullable=true)
     */
    protected $financialHistoryStatus;

    /**
     * Set the financial history status
     *
     * @param int $financialHistoryStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setFinancialHistoryStatus($financialHistoryStatus)
    {
        $this->financialHistoryStatus = $financialHistoryStatus;

        return $this;
    }

    /**
     * Get the financial history status
     *
     * @return int
     */
    public function getFinancialHistoryStatus()
    {
        return $this->financialHistoryStatus;
    }
}
