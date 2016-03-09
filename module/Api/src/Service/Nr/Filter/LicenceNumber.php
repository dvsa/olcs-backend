<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter;

use Zend\ServiceManager\FactoryInterface;
use Zend\Filter\AbstractFilter as ZendAbstractFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Exception\Exception;

/**
 * Class LicenceNumber
 * @package Dvsa\Olcs\Api\Service\Nr\Filter
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceNumber extends ZendAbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  array $value
     * @throws Exception
     * @return array
     */
    public function filter($value)
    {
        $explodedCommunityLicence = explode('/', $value['communityLicenceNumber']);

        if (count($explodedCommunityLicence) === 3) {
            $value['licenceNumber'] = $explodedCommunityLicence[1];
            return $value;
        }

        throw new Exception('Could not extract the licence number from community licence');
    }
}
