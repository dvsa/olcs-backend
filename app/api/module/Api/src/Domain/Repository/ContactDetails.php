<?php

/**
 * ContactDetails
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as Entity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;

/**
 * ContactDetails
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContactDetails extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @param array $contactParams Array of data as defined by Dvsa\Olcs\Transfer\Command\Partial\ContactDetails
     * @return array
     */
    public function populateRefDataReference(array $contactParams)
    {
        if (!empty($contactParams['address']['countryCode'])) {
            $contactParams['address']['countryCode'] = $this->getReference(
                Country::class, $contactParams['address']['countryCode']
            );
        }

        if (!empty($contactParams['phoneContacts'])) {
            foreach ($contactParams['phoneContacts'] as $i => $phoneContact) {
                $contactParams['phoneContacts'][$i]['phoneContactType']
                    = $this->getRefdataReference($phoneContact['phoneContactType']);
            }
        }

        return $contactParams;
    }
}
