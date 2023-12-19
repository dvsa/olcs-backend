<?php

/**
 * Get addresses by postcode
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Address;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Address\Service\AddressServiceAwareTrait;

/**
 * Get address by postcode
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler implements AddressServiceAwareInterface
{
    use AddressServiceAwareTrait;

    public function handleQuery(QueryInterface $query)
    {
        $addresses = $this->getAddressService()->fetchByPostcode($query->getPostcode());
        return [
            'result' => $addresses,
            'count' => count($addresses)
        ];
    }
}
