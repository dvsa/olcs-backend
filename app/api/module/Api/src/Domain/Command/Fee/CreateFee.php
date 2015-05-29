<?php

/**
 * Create Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Fee;

use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateFee extends AbstractCommand
{
    protected $application;

    protected $licence;

    protected $task;

    protected $amount;

    protected $invoicedDate;

    protected $feeType;

    protected $description;

    protected $feeStatus = Fee::STATUS_OUTSTANDING;

    /**
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getInvoicedDate()
    {
        return $this->invoicedDate;
    }

    /**
     * @return mixed
     */
    public function getFeeType()
    {
        return $this->feeType;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFeeStatus()
    {
        return $this->feeStatus;
    }
}
