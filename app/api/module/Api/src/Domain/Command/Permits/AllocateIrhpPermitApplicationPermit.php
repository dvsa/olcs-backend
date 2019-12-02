<?php

/**
 * Allocate IRHP Permit Application Permit
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

class AllocateIrhpPermitApplicationPermit extends AbstractCommand
{
    use Identity;

    /**
     * @var String
     * @Transfer\Optional
     * @Transfer\Filter({"name":"Zend\Filter\StringTrim"})
     * @Transfer\Validator({"name":"Zend\Validator\InArray","options":{"haystack":{"emissions_cat_euro6", "emissions_cat_euro5"}}})
     */
    protected $emissionsCategory = null;

    /**
     * @var \DateTime
     * @Transfer\Optional
     */
    protected $expiryDate;

    /**
     * @return string|null
     */
    public function getEmissionsCategory()
    {
        return $this->emissionsCategory;
    }

    /**
     * Gets the value of expiryDate.
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }
}
