<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licence history status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait LicenceHistoryStatusField
{
    /**
     * Licence history status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="licence_history_status", nullable=true)
     */
    protected $licenceHistoryStatus;

    /**
     * Set the licence history status
     *
     * @param int $licenceHistoryStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLicenceHistoryStatus($licenceHistoryStatus)
    {
        $this->licenceHistoryStatus = $licenceHistoryStatus;

        return $this;
    }

    /**
     * Get the licence history status
     *
     * @return int
     */
    public function getLicenceHistoryStatus()
    {
        return $this->licenceHistoryStatus;
    }
}
