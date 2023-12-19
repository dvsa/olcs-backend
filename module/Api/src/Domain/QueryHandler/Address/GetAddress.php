<?php

/**
 * Get address by uprn
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Address;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Address\Service\AddressServiceAwareTrait;

/**
 * Get address by uprn
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetAddress extends AbstractQueryHandler implements AddressServiceAwareInterface
{
    use AddressServiceAwareTrait;

    public function handleQuery(QueryInterface $query)
    {
        $uprn = $query->getUprn();
        return [
            'result' => [$this->getAddressService()->fetchByUprn($uprn)],
            'count' => 1
        ];
    }
}
