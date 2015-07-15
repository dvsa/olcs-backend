<?php

/**
 * CreateAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\CompaniesHouse;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateAlert extends \Dvsa\Olcs\Transfer\Command\AbstractCommand
{
    /**
     * @var string
     */
    protected $companyNumber;

    /**
     * @var array
     */
    protected $reasons = array();

    /**
     * Gets the value of companyNumber.
     *
     * @return string
     */
    public function getCompanyNumber()
    {
        return $this->companyNumber;
    }

    /**
     * Gets the value of reasons.
     *
     * @return array
     */
    public function getReasons()
    {
        return $this->reasons;
    }
}
