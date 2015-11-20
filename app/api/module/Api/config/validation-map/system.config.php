<?php

use Dvsa\Olcs\Api\Domain\CommandHandler;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\IsInternalUser;

return [
    CommandHandler\System\CreateFinancialStandingRate::class     => IsInternalUser::class,
    CommandHandler\System\DeleteFinancialStandingRateList::class => IsInternalUser::class,
    CommandHandler\System\UpdateFinancialStandingRate::class     => IsInternalUser::class,
];
