<?php

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessFeeWithId;

return [
    CommandHandler\Fee\CreateOverpaymentFee::class => IsInternalUser::class,
    CommandHandler\Fee\ResetFees::class => IsInternalUser::class,
    QueryHandler\Fee\Fee::class => CanAccessFeeWithId::class,
    QueryHandler\Fee\InterimRefunds::class => IsInternalUser::class,
    CommandHandler\Fee\UpdateFeeStatus::class => IsInternalUser::class
];
