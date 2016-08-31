<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsExternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Bus\Ebsr\CanUpdateTxcInboxRecord;
use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessBusReg;

return [
    QueryHandler\Bus\BusNoticePeriodList::class                                         => IsInternalUser::class,
    QueryHandler\Bus\BusServiceTypeList::class                                          => IsInternalUser::class,
    QueryHandler\BusRegSearchView\BusRegSearchViewList::class                           => NoValidationRequired::class,
    QueryHandler\BusRegSearchView\BusRegSearchViewContextList::class                    => NoValidationRequired::class,
    QueryHandler\Bus\SearchViewList::class                                              => IsInternalUser::class,

    // External users
    CommandHandler\Bus\Ebsr\UpdateTxcInbox::class                               => CanUpdateTxcInboxRecord::class,
    QueryHandler\Bus\Ebsr\BusRegWithTxcInbox::class                             => CanAccessTxcInboxRecord::class,
    QueryHandler\Bus\RegistrationHistoryList::class                             => CanAccessBusReg::class,

];
