<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    QueryHandler\Bus\BusNoticePeriodList::class                                         => IsInternalUser::class,
    QueryHandler\Bus\BusServiceTypeList::class                                          => IsInternalUser::class,
    QueryHandler\BusRegSearchView\BusRegSearchViewList::class                           => NoValidationRequired::class
];
