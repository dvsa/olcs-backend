<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Address;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\AddressHelper\AddressHelperService;
use Dvsa\Olcs\DvsaAddressService\Client\Mapper\AddressMapper;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class GetAddress extends AbstractQueryHandler
{
    public function __construct(protected AddressHelperService $addressHelperService)
    {
    }

    public function handleQuery(QueryInterface|\Dvsa\Olcs\Transfer\Query\Address\GetAddress $query)
    {
        $addresses = $this->addressHelperService->lookupAddress($query->getUprn());

        return [
            'result' => AddressMapper::convertAddressObjectsToArrayRepresentation($addresses),
            'count' => count($addresses),
        ];
    }
}
