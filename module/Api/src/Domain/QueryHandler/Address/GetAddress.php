<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Address;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\AddressHelper\AddressHelperService;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class GetAddress extends AbstractQueryHandler
{
    public function __construct(protected AddressHelperService $addressHelperService)
    {
    }

    public function handleQuery(QueryInterface $query)
    {
        $uprn = $query->getUprn();
        return [
            'result' => $this->addressHelperService->lookupAddress($uprn),
            'count' => 1
        ];
    }
}
