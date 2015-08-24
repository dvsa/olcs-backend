<?php

/**
 * Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Addresses extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    public function handleQuery(QueryInterface $query)
    {
        $licence = $this->getRepo()->fetchWithAddressesUsingId($query);

        return $this->result(
            $licence,
            [
                'correspondenceCd' => [
                    'address' => [
                        'countryCode',
                    ],
                    'contactType',
                    'phoneContacts' => [
                        'phoneContactType',
                    ],
                ],
                'establishmentCd' => [
                    'address' => [
                        'countryCode',
                    ],
                    'contactType'
                ],
                'transportConsultantCd' => [
                    'address' => [
                        'countryCode',
                    ],
                    'contactType',
                    'phoneContacts' => [
                        'phoneContactType',
                    ],
                ],
            ]
        );
    }
}
