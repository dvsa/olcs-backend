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
     * @param mixed $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @param mixed $licence
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getInvoicedDate()
    {
        return $this->invoicedDate;
    }

    /**
     * @param mixed $invoicedDate
     */
    public function setInvoicedDate($invoicedDate)
    {
        $this->invoicedDate = $invoicedDate;
    }

    /**
     * @return mixed
     */
    public function getFeeType()
    {
        return $this->feeType;
    }

    /**
     * @param mixed $feeType
     */
    public function setFeeType($feeType)
    {
        $this->feeType = $feeType;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFeeStatus()
    {
        return $this->feeStatus;
    }

    /**
     * @param string $feeStatus
     */
    public function setFeeStatus($feeStatus)
    {
        $this->feeStatus = $feeStatus;
    }
}
