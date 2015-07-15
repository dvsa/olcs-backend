<?php

/**
 * CreateAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\CompaniesHouse;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;

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
     * @var OrganisationEntity
     */
    protected $organisation;

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

    /**
     * Gets the value of organisation.
     *
     * @return OrganisationEntity
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }
}
