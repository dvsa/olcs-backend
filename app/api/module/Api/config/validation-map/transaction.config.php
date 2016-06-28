<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessTransactionWithRef;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessTransactionWithId;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Fee\CanPayOutstandingFees;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;

return [
    CommandHandler\Transaction\ReverseTransaction::class => IsInternalUser::class,
    CommandHandler\Transaction\AdjustTransaction::class => IsInternalUser::class,
    CommandHandler\Transaction\CompleteTransaction::class => CanAccessTransactionWithRef::class,
    CommandHandler\Transaction\PayOutstandingFees::class => CanPayOutstandingFees::class,
    // No validation required, as it will return a list of cards linked to the current user ID
    QueryHandler\Cpms\StoredCardList::class => NoValidationRequired::class,
    QueryHandler\Transaction\Transaction::class => CanAccessTransactionWithId::class,
    QueryHandler\Transaction\TransactionByReference::class => CanAccessTransactionWithRef::class,
    // Possibly this is no longer used (see handler comment), but can;t validate data as it is not linked to any
    // user/organisation data.
    QueryHandler\Fee\GetLatestFeeType::class => NoValidationRequired::class,
];
